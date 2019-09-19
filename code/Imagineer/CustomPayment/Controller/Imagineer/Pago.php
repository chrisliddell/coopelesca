<?php

namespace Imagineer\CustomPayment\Controller\Imagineer;

class Pago extends \Magento\Framework\App\Action\Action
{
    
    protected $_context;
    protected $_pageFactory;
    protected $_jsonEncoder;
    protected $cartManagementInterface;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
    ) {
        $this->_context = $context;
        $this->_pageFactory = $pageFactory;
        $this->_jsonEncoder = $encoder;        
        $this->cartManagementInterface = $cartManagementInterface;
        
        parent::__construct($context);
    }
    
    public function execute() 
    {
        /*
        The IdAlmacen field is required.; idAlmacenCredito
        The IdCuenta field is required.; idCuentaCreditoLista
        The IdTipoEntrega field is required.; idTipoEntregaCredito
        The TipoFactura field is required tipoCompra
        
        $response = array('status'=>'success');
        $this->getResponse()->representJson($this->_jsonEncoder->encode($response));*/
        $host = "almacenvirtualdev.coopelesca.co.cr";
        $ws = "http://ws.coopelesca.co.cr/wsAlmacenVirtual/api/";
       
        if( (!empty($_POST)) && isset($_POST['x_response_code']) && ($_POST['x_response_code'] == '1') ){        
        
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
            $customerSession = $objectManager->get('\Magento\Customer\Model\Session');
            $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
            $quoteManagement = $objectManager->create('\Magento\Quote\Api\CartManagementInterface');

            $tipo_factura = $customerSession->getTipoCompraOrder();
            $tipo_factura_temp = "";
            //Definir si es un producto de pedido especial
            $itemsVisible = $cart->getQuote()->getAllItems();
            //Categoría para pedidos especiales
            $catPedidoEspecial = "278";
            foreach ($itemsVisible as $_index => $productoExistente) {   
                $productIdExistente = $productoExistente->getProduct()->getCategoryIds();  
                
                if( array_search($catPedidoEspecial, $productIdExistente) === true ){
                    $tipo_factura_temp = "ESP";
                }  
            }
            $provincia = $customerSession->getProvinciaOrder();            
            $canton = $customerSession->getCantonOrder();
            $distrito = $customerSession->getDistritoOrder();
            $poblado = $customerSession->getPobladoOrder();
            $direccionExacta = $customerSession->getDireccionExactaOrder();

            //Solamente permito guardar si está logueado y si tiene el check de guardar la direccion
            if( ($customerSession->isLoggedIn()) && ($cart->getQuote()->getShippingAddress()->getSaveInAddressBook() == "1") ){
                $cedulaAsociado = $customerSession->getId();
                $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
                $address = $addresss->create();                
                $address->setCustomerId($cedulaAsociado)
                ->setFirstname($cart->getQuote()->getShippingAddress()->getData("firstname"))                 
                ->setLastname($cart->getQuote()->getShippingAddress()->getData("lastname"))
                ->setCountryId($cart->getQuote()->getShippingAddress()->getData("country_id"))
                ->setRegionId($provincia)
                ->setPostcode("00000000") // postcode se eliminó como requerido
                ->setCity($provincia) //city se eliminó como requerido
                ->setCounty($canton)
                ->setDistrict($distrito)
                ->setTown($poblado)
                ->setTelephone($cart->getQuote()->getShippingAddress()->getData("telephone"))            
                ->setCompany($cart->getQuote()->getShippingAddress()->getData("company"))
                ->setStreet($direccionExacta)
                ->setIsDefaultBilling('0')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
                $address->save();
            }    

            if($tipo_factura == "CON"){

                $montoTotal = '0.00';
                $plazo = 0;
                $prima =  '0.00';
                $cuota = '0.00';
                $montoTotalFinanciaminto = '0.00';
                $montoInteres = '0.00';
                $tasaInteres =  0;
                $baseFinanciamiento = '0.00';
                $contratoCredito = 0;

            }else{

                $montoTotal = ( ( $customerSession->getMonto() != 0) ? number_format($customerSession->getMonto(), 2, '.', '') : '0.00' );
                $plazo = ( ($customerSession->getPlazo() != 0) ? $customerSession->getPlazo() : 0 );
                $prima = ( ($customerSession->getPrima() != 0) ?  number_format($customerSession->getPrima(), 2, '.', '') : '0.00' );
                $cuota = ( ($customerSession->getCuotaOrder() != 0) ?  number_format($customerSession->getCuotaOrder(), 2, '.', '') : '0.00' );
                $montoTotalFinanciaminto = ( ( $customerSession->getTotalOrder() != 0) ?  number_format($customerSession->getTotalOrder(), 2, '.', '') : '0.00' );
                $montoInteres = ( ($customerSession->getInteresOrder() != 0) ?   number_format($customerSession->getInteresOrder(), 2, '.', '') : '0.00' );
                $tasaInteres =  ( ($customerSession->getTasaOrder() != 0) ?   $customerSession->getTasaOrder() : 0 );
                $baseFinanciamiento = ( ($customerSession->getBaseFinancia() != 0) ? number_format($customerSession->getBaseFinancia(), 2, '.', '') : '0.00' );
                $contratoCredito = ( ($customerSession->getContratoOrder() != 0) ? trim($customerSession->getContratoOrder()) : 0 );
            
            } 

            $id_almacen = $customerSession->getAlmacen();
            $id_cuenta = $customerSession->getCedula();
            $tipo_entrega = $customerSession->getTipoEntregaOrder(); 

            if($tipo_factura == "CON"){
                $costo = ( (isset($_POST['x_amount']))&&($_POST['x_amount'] != 0) ? number_format($_POST['x_amount'], 2, '.', '') : '0.00' );
                $costo_envio = ( ((int) $customerSession->getCostoEntregaOrder() != 0) ? number_format($customerSession->getCostoEntregaOrder(), 2, '.', '') : '0.00' );
                $autorizado_retirar = ( (trim($customerSession->getAutorizadoOrder()) != "") ?  trim($customerSession->getAutorizadoOrder())  : "ND" );
                $articulos = substr_replace(base64_decode(trim($customerSession->getListaOrder())), "", -1);
            }else{
                $costo = $montoTotal;    
                $costo_envio = ( ((int) $customerSession->getCostoEntregaOrder() != 0) ? number_format($customerSession->getCostoEntregaOrder(), 2, '.', '') : '0.00' );
                $autorizado_retirar = ( (trim($customerSession->getAutorizadoOrder()) != "") ?  trim($customerSession->getAutorizadoOrder())  : "ND" );
                $articulos = substr_replace(base64_decode(trim($customerSession->getListaOrder() )), "", -1);
            }
            
            $porcentaje_descuento = '0.00';
            $monto_descuento = $customerSession->getMontoDescuentoOrder();

            
            $totalCoope = ($cart->getQuote()->getSubtotal()+$costo_envio) - $monto_descuento;
            //Cargar valores del monto total financiamiento solo cuando es FIN
            $datosFinanciamiento = '"DatosFinanciamiento": {"MontoTotal": '.$montoTotal.',"Plazo": '.$plazo.',"Prima": '.$prima.',"Producto": '.$contratoCredito.',"Cuota": '.$cuota.',"BaseFinanciamiento": '.$baseFinanciamiento.',"MontoTotalFinanciamiento": '.(($tipo_factura == "CON") ? $totalCoope : $montoTotalFinanciaminto).',"Intereses": '.$montoInteres.',"TasaInteres": '.$tasaInteres.' },';


            /*** CREAR ORDEN EN MAGENTO ***/
            $quote = $cart->getQuote();
            $quote->getShippingAddress()->addData(array(
                 'city' => 'Coopelesca',
                 'postcode' => '000000000'
             ));
            $quote->getBillingAddress()->addData(array(
                 'city' => 'Coopelesca',
                 'postcode' => '000000000'
             ));
            $quote->setCustomerId(null)
                ->setCustomerEmail($quote->getBillingAddress()->getEmail())
                ->setCustomerIsGuest(true);
            $quote->setPaymentMethod('checkmo'); //Definir el tipo de pago
            $quote->setInventoryProcessed(false); //No afectar inventario
            $quote->save(); //Salvar cotización
            $quote->getPayment()->importData(['method' => 'checkmo']);                 
            $quote->collectTotals()->save();
            $order = $quoteManagement->submit($quote); // Crear orden             
            $increment_id = $order->getIncrementId(); //Obtener el ID  
            /*********************/
            
            //SI EL TIPO DE FACTURA ESTÁ EN BLANCO EL PEDIDO ES NORMAL SINO DEFINIR QUE ES PEDIDO ESPECIAL
            if(trim($tipo_factura_temp) != ""){
                $tipo_factura = "ESP";
            }
            $datosGenerales = '{"IdAlmacen": "'.$id_almacen.'","IdCuenta": "'.$id_cuenta.'","IdTipoEntrega": "'.$tipo_entrega.'","IdPedido": "'.$increment_id.'","TipoFactura": "'.$tipo_factura.'","Costo": '.$totalCoope.',"CostoEnvio": '.$costo_envio.',"PorcentajeDescuento": '.$porcentaje_descuento.',"MontoDescuento": '.$monto_descuento.',"AutorizadoRetirar": "'.$autorizado_retirar.'",';
                      
            $listaArticulos = '"ListaArticulos": [';
            $listaArticulos .= ( ( $articulos != "") ?  $articulos  : "{}" );
            $listaArticulos .= '],';


            $direccionEnvio = '"DireccionEnvio":{"Provincia": "'.$provincia.'","Canton": "'.$canton.'","Distrito": "'.$distrito.'","Poblado": "'.$poblado.'","DireccionExacta":"'.$direccionExacta.'"},';

            

            $boleta_cheque = ( ( isset($_POST['x_invoice_num']) ) ?  (int) $_POST['x_invoice_num'] : 0 );
            $codigo_autorizacion = ( (isset($_POST['x_auth_code']) ) ?  (int) $_POST['x_auth_code'] : 0 );

            $datosPagoTarjeta = '"DatosPagoTarjeta": {"ChequeBoleta": "'.$boleta_cheque.'","CodigoAutorizacion": "'.$codigo_autorizacion.'"} }';

            $registraPedido = $datosGenerales.$listaArticulos.$direccionEnvio.$datosFinanciamiento.$datosPagoTarjeta;
            /** LOG REGISTRO PEDIDO **/
            $req_dump = print_r($registraPedido, TRUE);
            $fp = fopen('/var/log/log-registro.log', 'a+');
            fwrite($fp, $req_dump);
            fclose($fp);
            /***********************/
            if( (($codigo_autorizacion != 0)&&($boleta_cheque != 0))||(($tipo_factura == "FIN")&&($prima == 0)) ){

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $ws."Pedido/RegistrarPedido",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 500,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $registraPedido,
                  CURLOPT_HTTPHEADER => array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);
                $mensaje_error = ""; 
                if ($err) {
                  $mensaje_error = '{"DescripcionRespuesta":"'.$err.'"}';
                } else {
                  $mensaje_error = $response;
                } 
                $mensaje_error = json_decode($mensaje_error);
            	$mensajeDescripcion = $mensaje_error->DescripcionRespuesta;
            }else{
            	$mensaje_error = "Código autorizacion pasarela de pago no definido";
            	$mensajeDescripcion = "Código autorizacion pasarela de pago no definido";
            }
                         
            /******/
            $langCode = $customerSession->getIdioma();
            $pos = strpos($langCode, "es");

             if($pos !== false){
                $idioma = "es";
             }else{
                $idioma = "en";
             }
            
             if(trim($id_almacen) == ""){
                $id_almacen = "T001";
             }
             $id_almacen = strtoupper($id_almacen);
             /*******/

            /*****/
            $finalizacionCompra="https://".$host."/".$id_almacen."_".$idioma.'/finalizacion-de-orden?monto_descuento='.$monto_descuento.'&orden='.$increment_id."&total=".$totalCoope."&direccion=".$direccionExacta."&costo_envio=".$costo_envio."&mensaje_coope=".$mensajeDescripcion;
             
            if( ($tipo_factura == "CON")||($tipo_factura == "ESP") ){      
                if($costo_envio == 0){
                    $costo_envio = "envío gratuito";
                }else{
                    $costo_envio = $costo_envio;
                }

                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                   echo $finalizacionCompra;
                   die();
                }else{                                      
                    return $this->resultRedirectFactory->create()->setUrl($finalizacionCompra);
                }
                
            }else{            
                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo $finalizacionCompra;
                    die();
                }else{                        
                    return $this->resultRedirectFactory->create()->setUrl($finalizacionCompra);
                }
            }
            /******/

        }else{

             if( (!empty($_POST)) && isset($_POST['x_response_code']) && ($_POST['x_response_code'] == '3') ){

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
                $langCode = $customerSession->getIdioma();
                $pos = strpos($langCode, "es");

                    if($pos !== false){
                        $idioma = "es";
                    }else{
                        $idioma = "en";
                    }

                $id_almacen = $customerSession->getAlmacen();
                    if(trim($id_almacen) == ""){
                        $id_almacen = "T001";
                    }
                $id_almacen = strtoupper($id_almacen);

                $cancelada = "https://".$host."/".$id_almacen."_".$idioma."/checkout";

                if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo $cancelada;
                    return ;
                }else{                        
                    return $this->resultRedirectFactory->create()->setUrl($cancelada);
                }
                

             }else{
                 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                 $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
                 $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');
                 
                 $id_almacen = $customerSession->getAlmacen();
                 if(trim($id_almacen) == ""){
                    $id_almacen = "T001";
                 }
                                  
                 $langCode = $customerSession->getIdioma();
                 $pos = strpos($langCode, "es");
                 if($pos !== false){
                    $idioma = "es";
                 }else{
                    $idioma = "en";
                 }
                                 
                 $messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
                 if(!(empty($_POST))){
                  $mensajeDescripcion = $_POST['x_response_reason_text'];
                 }else{
                  $mensajeDescripcion = "Sin parámetros";  
                 }
                 $messageManager->addNotice($mensajeDescripcion);

                 $direccionCompra="https://".$host."/".$id_almacen."_".$idioma."/checkout#shipping";             
                 if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo $direccionCompra;
                    die();
                }else{                        
                    return $this->resultRedirectFactory->create()->setUrl($direccionCompra);
                }
            }     
        }


    }
}
