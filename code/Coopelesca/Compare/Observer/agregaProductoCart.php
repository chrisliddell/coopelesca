<?php

namespace Coopelesca\Compare\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;

class agregaProductoCart implements ObserverInterface
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
        $storeManager = $om->get('\Magento\Store\Model\StoreManagerInterface');
        // Obtener el ID desde el detalle del producto
        $productId = (int)$observer->getRequest()->getParam('product');
        $productQty = (int)$observer->getRequest()->getParam('qty');
        
          
        //Almacen con idioma
        $codigoAlmacen = $storeManager->getStore()->getCode();
        $storeManager->setCurrentStore($codigoAlmacen);
        //Almacen e idioma
        $codigosAlmacenSeparados = explode("_", $codigoAlmacen);

        if(isset($codigosAlmacenSeparados)){
            $almacen = $codigosAlmacenSeparados[0];
            $langAlmacen = $codigosAlmacenSeparados[1];
        }else{
            $almacen = "T001";
            $langAlmacen = "es";
        }

        $pos = strpos($langAlmacen, "es");
        $catPedidoEspecial = "278";
        $bandera = true;

        
        $product = $om->get(\Magento\Catalog\Model\Product::class)->load($productId);
        $actualProductCategories = $product->getCategoryIds();

        $codigoAlmacen = $storeManager->getStore()->getCode();    
        $codigosAlmacenSeparados = explode("_", $codigoAlmacen);
        
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
                  
        if($producto_data->Articulo != null ){
            if( ($producto_data->CodigoRespuesta == "-200000")||($producto_data->Articulo->Cantidad <= 0)||($productQty > $producto_data->Articulo->Cantidad) ){
                                                                
                foreach ($itemsVisible as $_index => $productoExistente) {
                                                            
                    if($productoExistente->getData("sku") == $product->getSku()){
                        $cart->getQuote()->removeItem($productoExistente->getItemId());
                        $cart->getQuote()->collectTotals()->save();

                        $quote = $cartSession->getQuote(); 
                        $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
                    }
                    
                }
                $this->messageManager->getMessages(true);
                if($pos !== false){
                    $this->messageManager->addError(__("Lo sentimos, la cantidad del artículo seleccionado no está disponible. Por favor comuníquese con uno de nuestros agentes en línea") );
                }else{
                    $this->messageManager->addError(__("We're sorry, the selected item quantity isn't available in the actual warehouse.\nPlease contact one of our agents."));
                } 

               
            }else{   
                
                foreach ($itemsVisible as $_index => $productoExistente) {
                    
                    $categoriasProducto = $productoExistente->getProduct()->getCategoryIds();  
                  
                    //No tengo productos de pedido especial en el carrito                 
                    if( array_search($catPedidoEspecial, $categoriasProducto) === false ){    
                        if( array_search($catPedidoEspecial, $actualProductCategories) === false ){
                            $bandera = true;             
                        }else{
                            $bandera = false;
                            break;
                        }                                               
                    }else{	
                        //¿ El que estoy agregando actualmente tiene pedido especial ?        	
                        if( array_search($catPedidoEspecial, $actualProductCategories) === false ){
                            $bandera = false;  
                            break;           
                        }//de otra forma insertelo
                    }

                }

                if(!$bandera){

                    foreach ($itemsVisible as $_index => $productoExistente) {
                                                            
                        if($productoExistente->getData("sku") == $product->getSku()){
                            $cart->getQuote()->removeItem($productoExistente->getItemId());
                            $cart->getQuote()->collectTotals()->save();
    
                            $quote = $cartSession->getQuote(); 
                            $quote->setTotalsCollectedFlag(false)->collectTotals()->save();
                        }
                        
                    }

                    $this->messageManager->getMessages(true);
                    if($pos !== false){
                        $this->messageManager->addWarning(__('Únicamente se pueden agregar productos de pedido especial con otros de su misma categoría'));
                    }else{
                        $this->messageManager->addWarning(__('Only articles that belong to the same type of category or subcategory can be compared.')); 
                    }                    

                    $bandera = true;
                }else{

                    //Almacen con idioma
                    $codigoAlmacen = $storeManager->getStore()->getCode();
                    $storeManager->setCurrentStore($codigoAlmacen);
                    //Almacen e idioma
                    $codigosAlmacenSeparados = explode("_", $codigoAlmacen);

                    if(isset($codigosAlmacenSeparados)){
                        $almacen = strtoupper($codigosAlmacenSeparados[0]);
                        $langAlmacen = $codigosAlmacenSeparados[1];
                    }else{
                        $almacen = "T001";
                        $langAlmacen = "es";
                    }


                    $this->messageManager->getMessages(true);
                    if($pos !== false){
                        $this->messageManager->addSuccess("Producto agregado al carrito correctamente, <a id='carritoPagar' href='/".$almacen."_".$langAlmacen."/checkout' title='Ir al carrito'>&nbsp;Ir al carrito >> </a>");	
                    }else{
                        $this->messageManager->addSuccess("Product added to the cart correctly, <a id='carritoPagar' href='/".$almacen."_".$langAlmacen."/checkout' title='Go to cart'> &nbsp; Go to cart >> </a>");
                    }

                }    
                
            }
        }
                    
                    
                
            
        }
        
    }
}