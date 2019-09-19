<?php

namespace Coopelesca\Compare\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\App\ObjectManager;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Helper\Product\Compare;
use Magento\Framework\Controller\ResultFactory; 

class agregaProductoComp implements ObserverInterface
{
    /** @var \Magento\Framework\Message\ManagerInterface */
    protected $messageManager;

    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->messageManager = $managerInterface;
        $this->objectManager = ObjectManager::getInstance();
        $this->url = $url;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        
        $om = \Magento\Framework\App\ObjectManager::getInstance();  
        $resolver = $om->get('Magento\Framework\Locale\Resolver');
        $langCode = $resolver->getLocale();
        $pos = strpos($langCode, "es");
        $storeManager = $om->get('\Magento\Store\Model\StoreManagerInterface');

        $codigoAlmacen = $storeManager->getStore()->getCode();    
        $codigosAlmacenSeparados = explode("_", $codigoAlmacen);

        if($observer->getRequest()->getParam('product')){
            if( $pos !== false ){    
                $this->messageManager->getMessages(true);                
                $this->messageManager->addSuccess('El producto ha sido agregado a la <a href="/'.strtoupper($codigosAlmacenSeparados[0]).'_es/catalog/product_compare/index/">lista de comparación</a>');   
            }else{
                $this->messageManager->getMessages(true);                    
                $this->messageManager->addSuccess('You added the product to the <a href="/'.strtoupper($codigosAlmacenSeparados[0]).'_en/catalog/product_compare/index/" >comparison list</a>'); 
            }

            return $this;
        }   
    

    }
}