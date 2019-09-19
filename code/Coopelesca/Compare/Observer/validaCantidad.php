<?php

namespace Coopelesca\Compare\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
class validaCantidad implements ObserverInterface{

/**
* @var \Magento\Framework\Message\ManagerInterface
*/
protected $_messageManager,$objectManager;

/**
* @param \Magento\Framework\Message\ManagerInterface $messageManager
*/
public function __construct( \Magento\Framework\Message\ManagerInterface $messageManager ){
	$this->_messageManager = $messageManager;
    $this->objectManager = ObjectManager::getInstance();
}

/**
* add to cart event handler.
* @param \Magento\Framework\Event\Observer $observer
* @return $this
*/

public function execute(\Magento\Framework\Event\Observer $observer){

	$om = \Magento\Framework\App\ObjectManager::getInstance();  
	$resolver = $om->get('Magento\Framework\Locale\Resolver');
	$langCode = $resolver->getLocale();
	$pos = strpos($langCode, "es");

	$catPedidoEspecial = "278";

    //Obtener los productos del carrito
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
    $itemsVisible = $cart->getQuote()->getAllItems();
    //<><><><><><><><><><><><><><><><><>

    // Obtener el ID desde el detalle del producto
    $productId = (int)$observer->getRequest()->getParam('product');
    $productQty = (int)$observer->getRequest()->getParam('qty');
        
    $product = ObjectManager::getInstance()->get(\Magento\Catalog\Model\Product::class)->load($productId);
    $actualProductCategories = $product->getCategoryIds();
    $totalUdsCarrito = 0;
    
    $bandera = true;

	$product = $this->objectManager->get(\Magento\Catalog\Model\Product::class)->load($productId);
	$storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
	$codigoAlmacen = $storeManager->getStore()->getCode();
	//Almacen e idioma
	$codigosAlmacenSeparados = explode("_", $codigoAlmacen);

	$curl = curl_init();
	$sumCantidad = '{"Sku":"'.$product->getSku().'","IdAlmacen":"'.$codigosAlmacenSeparados[0].'"}';
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://ws.coopelesca.co.cr/wsAlmacenVirtual/api/Inventario/ConsultarArticulo",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $sumCantidad,
	  CURLOPT_HTTPHEADER => array(
	    "Cache-Control: no-cache",
	    "Content-Type: application/json"
	  ),
	));

	$response = curl_exec($curl);

	$err = curl_error($curl);
	curl_close($curl);
	$temp = print_r($response,true);
	$tot = "";
	if(!$err){	
	  $producto_data = json_decode($response);

		  if(!empty($itemsVisible)){
	    	
		    //Validar categorías de producto actual contra las que estan en el carrito
		    foreach ($itemsVisible as $_index => $productoExistente) {  
				if(isset($productoExistente)){              
					$productIdExistente = $productoExistente->getProduct()->getCategoryIds();  

					if((int)$productoExistente->getProduct()->getId() == (int)$productId){
						$totalTemp = (int)$productQty + (int)$productoExistente->getQty();
						if($totalTemp > $producto_data->Articulo->Cantidad){
							$productoExistente->setData("qty",1); 
							$cart->getQuote()->updateItem($productoExistente->getId(),array('qty'=>1));
							$cart->getQuote()->collectTotals()->save();
						}
					}
					

					//No tengo productos de pedido especial en el carrito                 
					if( array_search($catPedidoEspecial, $productIdExistente) === false ){    
						if( array_search($catPedidoEspecial, $actualProductCategories) === false ){
							$bandera = true;             
						}else{
							$bandera = false;
						}                       
					}else{	
						//¿ El que estoy agregando actualmente tiene pedido especial ?        	
						if( array_search($catPedidoEspecial, $actualProductCategories) === false ){
							$bandera = false;             
						}//de otra forma insertelo
					}

					if($producto_data->Articulo != null ){
						if( ($producto_data->CodigoRespuesta == "-200000")||($producto_data->Articulo->Cantidad <= 0)||($productQty > $producto_data->Articulo->Cantidad) ){
						  $productoExistente->setData("qty",1); 
						  $cart->getQuote()->updateItem($productoExistente->getId(),array('qty'=>1));
						  $cart->getQuote()->collectTotals()->save();
						}
				   }else{
					   $this->_messageManager->addError($producto_data->DescripcionRespuesta);	
				   }
				}
		    }
		}

	     
	    if( $bandera ){
	        //<><><><><><><><><> Mensaje custom al agregar al carrito <><><><><>//      
	        if($pos !== false){
	        	//$this->_messageManager->addSuccess("Producto agregado al carrito correctamente, <a id='carritoPagar' href='/checkout' title='Ir al carrito'>&nbsp;Ir al carrito >> </a>");	
	        }else{
	        	//$this->_messageManager->addSuccess("Product added to the cart correctly, <a id='carritoPagar' href='/checkout' title='Go to cart'> &nbsp; Go to cart >> </a>");
	        }        
	        
	        //><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><><        
	        //Obtener sesión actual del carrito
	        $checkoutManager = $this->objectManager->get('\Magento\Checkout\Model\Session');                  

	    }else{     
	    	if($pos !== false){
	        	$this->_messageManager->addWarning(__('Únicamente se pueden agregar productos de pedido especial con otros de su misma categoría'));
	        }else{
	        	$this->_messageManager->addWarning(__('Only articles that belong to the same type of category or subcategory can be compared.')); 
	        }

	                       
	         $observer->getRequest()->setParam('product', false);         
	    }
/*
	  if($producto_data->Articulo != null ){
		  if( ($producto_data->CodigoRespuesta == "-200000")||($producto_data->Articulo->Cantidad <= 0)||($productQty > $producto_data->Articulo->Cantidad) ){
			$productoExistente->setData("qty",1); 
			$cart->getQuote()->updateItem($productoExistente->getId(),array('qty'=>1));
			$cart->getQuote()->collectTotals()->save();
		  }
	 }else{
	 	$this->_messageManager->addError($producto_data->DescripcionRespuesta);	
	 }*/
	  

	}else{
	  $this->_messageManager->addError($response);
	}

}



}