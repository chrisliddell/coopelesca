<?php
namespace Coopelesca\Compare\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Type as ProductType;

class actualizaCart implements ObserverInterface{

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


	/**
	 * 
	 * ESTO SE EJECUTA CUANDO SE ACTUALIZA EL CARRITO
	 * O SE AGREGAN PRODUCTOS
	 * 
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
        $cartSession = $objectManager->get('Magento\Checkout\Model\Session');
        $itemsVisible = $cart->getQuote()->getAllItems();
        //<><><><><><><><><><><><><><><><><>

        $storeManager = $this->objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $codigoAlmacen = $storeManager->getStore()->getCode();
        //Almacen e idioma
        $codigosAlmacenSeparados = explode("_", $codigoAlmacen);
  
        // Obtener el ID desde el detalle del producto
        foreach ($itemsVisible as $_index => $productoExistente) {  
			
            $product = $objectManager->get(\Magento\Catalog\Model\Product::class)->load($_index);    
            
            $curl = curl_init();
                                
            $sumCantidad = '{"Sku":"'.$productoExistente->getData("sku").'","IdAlmacen":"'.$codigosAlmacenSeparados[0].'"}';
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
            if(!($response === false)){	                
            $producto_data = json_decode($response);

                        if($producto_data->Articulo != null ){
                            if( ($producto_data->CodigoRespuesta == "-200000")||($producto_data->Articulo->Cantidad <= 0)||( $productoExistente->getData("qty") > $producto_data->Articulo->Cantidad) ){
                                                                                                                          
                                $cart->getQuote()->removeItem($productoExistente->getItemId());                            
                                $cart->getQuote()->collectTotals()->save();

                                $quote = $cartSession->getQuote(); 
                                $quote->setTotalsCollectedFlag(false)->collectTotals()->save();  

                            }
                            
                        }else{
                            $this->_messageManager->addError($producto_data->DescripcionRespuesta);	                            
                        }
                       
            }else{
                $this->_messageManager->addError($response);
            }            
        }  
        
        

        return $this;
	}	
}