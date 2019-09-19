<?php

namespace Coopelesca\Compare\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\App\ObjectManager;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Helper\Product\Compare;
use Magento\Framework\Controller\ResultFactory; 

class agregaProducto implements ObserverInterface{

/**
* @var \Magento\Framework\Message\ManagerInterface
*/
protected $_messageManager;

/**
* @param \Magento\Framework\Message\ManagerInterface $messageManager
*/
public function __construct( \Magento\Framework\Message\ManagerInterface $messageManager ){
	$this->_messageManager = $messageManager;
}

public function execute(Observer $observer){

	$productId = (int)$observer->getRequest()->getParam('product');

        if ( $productId ){
            $product = ObjectManager::getInstance()->get(Product::class)->load($productId);
            $actualProductCategories = $product->getCategoryIds();      
            $coleccionProductos = ObjectManager::getInstance()->get(Compare::class)->getItemCollection();
            $om = \Magento\Framework\App\ObjectManager::getInstance();  
            $resolver = $om->get('Magento\Framework\Locale\Resolver');
            $langCode = $resolver->getLocale();
            $pos = strpos($langCode, "es");
            

            $bandera = false;
            $cantidadProductos = 0;
            foreach ($coleccionProductos as $_index => $productoExistente) {  
            
                if($productId == $productoExistente->getData("product_id")){
                    if( $pos !== false ){                 
                        $this->_messageManager->addWarning(__('Ya existe el producto en la lista de comparación')); 
                    }else{
                        $this->_messageManager->addWarning(__('The product is already on the list')); 
                    }

                    $cart = $om->get('\Magento\Checkout\Model\Cart');
                    $cartSession = $om->get('Magento\Checkout\Model\Session');
                    
                    $cart->getQuote()->removeItem($productoExistente->getItemId());
                    $cart->getQuote()->collectTotals()->save();

                    $quote = $cartSession->getQuote(); 
                    $quote->setTotalsCollectedFlag(false)->collectTotals()->save();

                }

                $categoriaEnLista = $productoExistente->getCategoryIds();               
                if(!(empty(array_diff($actualProductCategories, $categoriaEnLista)))){
                  $bandera = true;
                }  
                $cantidadProductos++;              
            }
                       
            if((isset($product) && $bandera)){
                 if( $pos !== false ){    
                    $this->_messageManager->addWarning(__('Únicamente se pueden comparar artículos que pertenezcan al mismo tipo de categoría o subcategoría.'));   
                 }else{
                    $this->_messageManager->addWarning(__('Only items that belong to the same category or subcategory type can be compared.')); 
                 } 

                 $observer->getRequest()->setParam('product', false);
                                        
            }

            if( ($cantidadProductos >= 3)&&(isset($product)) ){
                           
                if( $pos !== false ){                 
                    $this->_messageManager->addWarning(__('Solamente se pueden comparar tres productos a la vez')); 
                }else{
                    $this->_messageManager->addWarning(__('Only three products can be compared at a time')); 
                }
                                                    
                $observer->getRequest()->setParam('product', false);                
    	    }	
        }

        return $this;

    }
}