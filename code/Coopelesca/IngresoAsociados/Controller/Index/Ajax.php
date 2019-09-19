<?php
namespace Coopelesca\IngresoAsociados\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\Framework\App\ObjectManager;
use \Magento\Customer\Model\Customer;
use \Magento\Customer\Model\Session;

class Ajax extends  \Magento\Framework\App\Action\Action {

  protected $resultJsonFactory, $Almacenes, $datos, $CedulaAsociado, $ConsultaAsociado, $ClaveAsociado, $DatosAsociado;
  protected $sku, $precio, $cantidad, $peso, $largo, $ancho, $alto, $id_almacen, $id_cuenta;
  protected $envioCheck,$metodosEnvioCheck, $provinciaMetodo, $cantonMetodo,$distritoMetodo,$pobladoMetodo,$direccionMetodo;


  protected $idEnvio, $idPedido,$valores, $estaLogueado;
  protected $listadoProductos, $metodoEnvioCoopelesca, $autorizadoRecibeCoopelesca, $listaArticulosCreditos, $idCuentaCredito, $idAlmacenCredito, $montoCompraCredito,$plazoCredito,$primaCredito,$calculoCredito,$costoEnvio; 
  protected $SecretPassword,$contratos,$idCuentaContratos;
  protected $direccionEnvio,$totalCarrito,$provinciaDireccion,$cantonDireccion,$distritoDireccion,$pobladoDireccion,$exactaDireccion;

        public function __construct(
               Context  $context,
               \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
        ) {
            $this->Almacenes = $this->datos =  $this->CedulaAsociado = $this->ConsultaAsociado = "";  
            $this->resultJsonFactory = $resultJsonFactory;
            parent::__construct($context);
        }


        public function execute() {

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();        
          $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
          $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
          //URL DEL ENDPOINT
          $ws = "http://ws.coopelesca.co.cr/wsAlmacenVirtual/api/"; 

          //Almacen actual
          $almacen = "";
          //Lenguaje actual 
          $langAlmacen = "";
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

          if(isset($_POST['accion'])){
          $accion = filter_var($_POST['accion'],FILTER_SANITIZE_STRING);
          switch($accion){

            // Obtener la información para saber si es asociado
            case "1":
               $this->CedulaAsociado = filter_var($_POST['CedulaAsociadoLogin'],FILTER_SANITIZE_STRING);
               unset($_POST['claveAsociado']);
               $postdata = '{"CedulaAsociado":"'.$this->CedulaAsociado.'"}';
               $this->ConsultaAsociado = utf8_encode($this->EsAsociado($postdata,$ws));
               $result = $this->resultJsonFactory->create();
               return $result->setData(['ConsultaAsociado' => $this->ConsultaAsociado]);
               break;
            // Obtener información de todos los almacenes disponibles ( nombre - id )
            case "2":
                $this->Almacenes = $this->GetAlmacenes($ws);
                $result = $this->resultJsonFactory->create();
                return $result->setData(['Almacenes' => $this->Almacenes]);             
                break;
            // Validar contraseña del cliente    
            case "3":
                $this->CedulaAsociado = filter_var($_POST['CedulaAsociadoLogin'],FILTER_SANITIZE_STRING);
                $this->ClaveAsociado = filter_var($_POST['claveAsociado'],FILTER_SANITIZE_STRING);
                $this->DatosAsociado = json_decode($this->GetDatosAsociado($this->CedulaAsociado,$this->ClaveAsociado,$ws));
                                              
                if( (isset($this->DatosAsociado->CodigoRespuesta))&&($this->DatosAsociado->CodigoRespuesta == "0") ){
                    $res = $this->iniciarSesion($this->CedulaAsociado,$this->DatosAsociado->Token->PrivateCode,$ws);
                    $result = $this->resultJsonFactory->create();
                    if($res){                      
                      return $result->setData(['Asociado' => $this->DatosAsociado]);
                    }else{
                      if( $pos !== false ){
                        return $result->setData(['Mensaje' => 'Asociado no existe,clave o cédula incorrecta']);
                      }else{
                        return $result->setData(['Mensaje' => 'Associate does not exist, password or wrong id']);
                      }
                    }                    
                }else{
                    $result = $this->resultJsonFactory->create();
                    if( $pos !== false ){
                        return $result->setData(['Mensaje' => 'Asociado no existe o clave incorrecta, intente de nuevo']);                    
                    }else{
                        return $result->setData(['Mensaje' => 'Associate does not exist, password or wrong id, try again']);
                    }
                }
                break;
            // Cargar métodos de envío en detalle de producto   
            case "4":
                $this->sku = filter_var($_POST['sku'],FILTER_SANITIZE_STRING);
                $this->precio = filter_var($_POST['precio'],FILTER_SANITIZE_STRING);
                $this->cantidad = filter_var($_POST['cantidad'],FILTER_SANITIZE_STRING);
                $this->peso = filter_var($_POST['peso'],FILTER_SANITIZE_STRING);
                $this->largo = filter_var($_POST['largo'],FILTER_SANITIZE_STRING);
                $this->ancho = filter_var($_POST['ancho'],FILTER_SANITIZE_STRING);
                $this->alto = filter_var($_POST['alto'],FILTER_SANITIZE_STRING);
                $this->id_almacen = filter_var($_POST['id_almacen'],FILTER_SANITIZE_STRING);
                $this->id_cuenta = filter_var($_POST['id_cuenta'],FILTER_SANITIZE_STRING);

                $postdata = "{'IdCuenta':'".$this->id_cuenta."','IdAlmacen':'".$this->id_almacen."','ListaArticulos':[{ 'Sku': '".$this->sku."','IdAlmacen':'".$this->id_almacen."','Precio':'".$this->precio."','Cantidad':'".$this->cantidad."','Peso':'".$this->peso."','Largo':'".$this->largo."','Ancho':'".$this->ancho."','Alto':'".$this->alto."'}]}";

                $this->MetodosEnvio = json_decode($this->GetMetodosEnvio($postdata,$ws));
                $result = $this->resultJsonFactory->create();
                return $result->setData(['MetodosEnvio' => $this->MetodosEnvio]);      
                break;

             // Cargar métodos de envío en checkout    
            case "5":                              
                  $this->idCuentaCredito = (int)trim($_POST['idCuentaCredito']);
                  $this->idAlmacenCredito = trim($_POST['idAlmacenCredito']);
                  $this->listaArticulosCreditos = substr_replace(base64_decode($_POST['listaArticulosCreditos']), "", -1);

                  $this->provinciaMetodo = trim($_POST['provinciaMetodoEnvio']);
                  $this->cantonMetodo = trim($_POST['cantonMetodoEnvio']);
                  $this->distritoMetodo = trim($_POST['distritoMetodoEnvio']);
                  $this->pobladoMetodo = trim($_POST['pobladoMetodoEnvio']);
                  $this->direccionMetodo = trim($_POST['direccionMetodoEnvio']);



                  $this->direccionEnvio = "\"DireccionEnvio\": {\"Provincia\": \"".$this->provinciaMetodo."\",\"Canton\": \"".$this->cantonMetodo."\",\"Distrito\": \"".$this->distritoMetodo."\",\"Poblado\": \"".$this->pobladoMetodo."\",\"DireccionExacta\": \"".$this->direccionMetodo."\"}";

                  $postdata = "{\"IdAlmacen\":\"".$this->idAlmacenCredito."\", \"IdCuenta\":\"".$this->idCuentaCredito."\",\"ListaArticulos\":[".trim($this->listaArticulosCreditos)."],".$this->direccionEnvio."}";
                  /** LOG REGISTRO PEDIDO **/
                  $req_dump = print_r($postdata, TRUE);
                  $fp = fopen('/var/log/log-registro.log', 'a+');
                  fwrite($fp, $req_dump);
                  fclose($fp);
                  /***********************/                
                $this->metodosEnvioCheck = $this->GetMetodosEnvio($postdata,$ws);

                $result = $this->resultJsonFactory->create();
                return $result->setData(['metodosEnvioCheck' => $this->metodosEnvioCheck]);      
                break;
              // Estado del pedido
              case "6":
                  //IdPedido - IdEnvio
                  $this->idPedido = $_POST['textoEstadoPedido'];
                
                  $postdata = "{\"IdPedido\":\"".trim($this->idPedido)."\"}";
                  $this->metodosEstadoPedido = $this->GetEstadoPedido($postdata,$ws);

                  $result = $this->resultJsonFactory->create();
                  return $result->setData(['EstadoPedido' => $this->metodosEstadoPedido]);
                  break; 
              // Calculo de crédito
              case "7":

                  $this->metodoEnvioCoopelesca = (int)trim($_POST['metodoEnvioCoopelesca']);                  
                  $this->listaArticulosCreditos = substr_replace(base64_decode($_POST['listaArticulosCreditos']), "", -1);
                  $temp = (int)trim($_POST['CedulaAsociado']);
                  if($temp == 0){
                    if((int)trim($_POST['idCuentaCreditoLista']) == 0){
                      $this->idCuentaCredito = 0;
                    }else{
                      $this->idCuentaCredito = (int)trim($_POST['idCuentaCreditoLista']);
                    }                    
                  }else{
                    $this->idCuentaCredito = (int)trim($_POST['CedulaAsociado']);
                  }
                  

                  $this->idAlmacenCredito = trim($_POST['idAlmacenCredito']);
                  $this->montoCompraCredito = number_format($_POST['montoCompraCredito'], 2, '.', '');
                  $this->plazoCredito = (int)trim($_POST['plazoCredito']);
                  $this->primaCredito = number_format($_POST['primaCredito'], 2, '.', '');
                  $this->costoEnvio = number_format($_POST['costoEntregaCredito'], 2, '.', '');


                  
                  $postdata = "{\"IdAlmacenVirtual\":\"".$this->idAlmacenCredito."\", \"IdCuenta\":\"".$this->idCuentaCredito."\",\"MontoTotal\":".$this->montoCompraCredito.",\"Plazo\":".$this->plazoCredito.",\"Prima\":".$this->primaCredito.",\"CostoEnvio\":".$this->costoEnvio.",\"ListaArticulos\":[".trim($this->listaArticulosCreditos)."]}";

                  $this->calculoCredito = $this->GetCalculoCredito($postdata,$ws);

                  $result = $this->resultJsonFactory->create();
                  return $result->setData(['CalculoCredito' => $this->calculoCredito]); 
                  break;
              //OBTENER CONTRATOS  
              case "8":

                  $this->idCuentaContratos = (int)filter_var($_POST['idCuentaContrato'],FILTER_SANITIZE_STRING);
                  $postdata = "{\"IdCuenta\":\"".$this->idCuentaContratos."\"}";

                  $this->contratos = $this->GetContratos($postdata,$ws);

                  $result = $this->resultJsonFactory->create();
                  return $result->setData([ 'Contratos' => $this->contratos ]);                  
                  break;  
              //ALMACENAR DATOS EN SESION    
              case "9":
                  $listaOrder = $_POST['listaOrder'];
                  $cedulaOrder = $_POST['cedulaOrder'];
                  $almacenOrder = $_POST['almacenOrder'];
                  $idiomaOrder = $_POST['idiomaOrder'];
                  $montoOrder = $_POST['montoOrder'];
                  $plazoOrder = $_POST['plazoOrder'];
                  $primaOrder = $_POST['primaOrder'];                  
                  $baseFinanciaOrder = $_POST['baseFinanciaOrder'];
                  $tasaOrder = $_POST['tasaOrder'];
                  $cuotaOrder = $_POST['cuotaOrder'];
                  $interesMontoOrder = $_POST['interesMontoOrder'];
                  $totalMontoOrder = $_POST['totalMontoOrder'];
                  $contratoOrder = $_POST['contratoOrder'];
                  $costoMontoOrder = $_POST['costoMontoOrder'];
                  $tipoCompraOrder = trim($_POST['tipoCompraOrder']);
                  $autorizadoRetirarOrder = $_POST['autorizadoRetirarOrder'];
                  $idTipoEntregaOrder = (trim($_POST['idTipoEntregaOrder']) == "" ) ? "" : trim($_POST['idTipoEntregaOrder']);
                  $costoEntregaOrder = $_POST['costoEntregaOrder'];
                  $montoDescuentoOrder = $_POST['montoDescuentoOrder'];
                  $provinciaOrder = $_POST['provinciaOrder'];
                  $cantonOrder = $_POST['cantonOrder'];
                  $distritoOrder = $_POST['distritoOrder'];
                  $pobladoOrder = $_POST['pobladoOrder'];
                  $direccionOrder = $_POST['direccionOrder'];

                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
                  $customerSession->setCedula($cedulaOrder);                  
                  $customerSession->setListaOrder($listaOrder);
                  $customerSession->setAlmacen($almacenOrder);
                  $customerSession->setIdioma($idiomaOrder);
                  $customerSession->setMonto($montoOrder);
                  $customerSession->setPlazo($plazoOrder);
                  $customerSession->setPrima($primaOrder);
                  $customerSession->setBaseFinancia($baseFinanciaOrder);
                  $customerSession->setTasaOrder($tasaOrder);
                  $customerSession->setCuotaOrder($cuotaOrder);
                  $customerSession->setInteresOrder($interesMontoOrder);
                  $customerSession->setTotalOrder($totalMontoOrder);
                  $customerSession->setContratoOrder($contratoOrder);
                  $customerSession->setCostoOrder($costoMontoOrder);
                  $customerSession->setTipoCompraOrder($tipoCompraOrder);
                  $customerSession->setAutorizadoOrder($autorizadoRetirarOrder);
                  $customerSession->setCostoEntregaOrder($costoEntregaOrder);
                  $customerSession->setTipoEntregaOrder($idTipoEntregaOrder);
                  $customerSession->setMontoDescuentoOrder($montoDescuentoOrder);
                  $customerSession->setProvinciaOrder($provinciaOrder);
                  $customerSession->setCantonOrder($cantonOrder);
                  $customerSession->setDistritoOrder($distritoOrder);
                  $customerSession->setPobladoOrder($pobladoOrder);
                  $customerSession->setDireccionExactaOrder($direccionOrder);
                  
                  $result = $this->resultJsonFactory->create();
                  return $result->setData([ 'Mensaje' => "Sesión cliente OK" ]);  

            break;  
            //ACTUALIZAR EL MONTO A ENVIAR A PASARELA DE PAGO
            case "10":

              $login = $_POST['x_login'];
              $sequence = $_POST['x_fp_sequence'];
              $timestamp = $_POST['x_fp_timestamp'];
              $amount = $_POST['x_amount'];
              $transactionKey = "CoopeSuCurnTest";

              $fingerprint = $login . '^' . $sequence . '^' . $timestamp . '^' . $amount . '^';
              $hash = hash_hmac('md5', $fingerprint, $transactionKey);

              $this->valores = "{\"hash\":\"".$hash."\"}";

              $result = $this->resultJsonFactory->create();
              return $result->setData([ 'Mensaje' => $this->valores ]);

            break;   
            //OBTENER UN VALOR PARA SABER SI EL USUARIO ESTA LOGUEADO
            case "11":
             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
             $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
             $this->estaLogueado = "{\"estaLogueado\":\"".$customerSession->isLoggedIn()."\"}";

             $result = $this->resultJsonFactory->create();             
             return $result->setData([ 'Mensaje' => $this->estaLogueado ]); 
             break;
             //OBTENER TOTAL DEL CARRITO
             case "12":
             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
             $cart = $objectManager->get('\Magento\Checkout\Model\Cart');  

             $subTotal = $cart->getQuote()->getData("base_subtotal");
             $subtotal_condescuento = $cart->getQuote()->getData("subtotal_with_discount");
                          
             $this->totalCarrito = "{\"subtotal\":\"".$subTotal."\",\"subtotal_condescuento\":\"".$subtotal_condescuento."\"}";

             $result = $this->resultJsonFactory->create();             
             return $result->setData([ 'Mensaje' => $this->totalCarrito ]); 
             break;
             //OBTENER VALORES DE LA DIRECCION DE ENVIO
             case "13":
              $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
              $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
              $id_cuenta = $customerSession->getCustomerId();              
              
              $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($id_cuenta);
              $shippingAddress = $customer->getDefaultShippingAddress(); 
              $quoteManagement = $objectManager->create('\Magento\Checkout\Model\Session');
              
              if($shippingAddress){
                  $this->provinciaDireccion = $shippingAddress->getRegionId();
                  $this->cantonDireccion = $shippingAddress->getCounty();
                  $this->distritoDireccion = $shippingAddress->getDistrict();
                  $this->pobladoDireccion = $shippingAddress->getTown();
                  $this->exactaDireccion = $shippingAddress->getStreet();
              }

             $result = $this->resultJsonFactory->create();             
             return $result->setData([ 'provincia' => $this->provinciaDireccion,'canton' => $this->cantonDireccion,'distrito' => $this->distritoDireccion,'poblado' => $this->pobladoDireccion,'direccion' => $this->exactaDireccion ]);   
             break;
             //DEFINIR ALMACEN ACTUAL
             case "14":
               $almacen = filter_var($_POST['almacen'],FILTER_SANITIZE_STRING);
               $objectManager = \Magento\Framework\App\ObjectManager::getInstance();                         
               $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface'); 
                                          
                $stores = $storeManager->getStores(true, false);
                foreach($stores as $store){
                  if($store->getCode() === $almacen){
                      $store_id = $store->getId();
                  }
                }   

               $storeManager->setCurrentStore($store_id);            

               $result = $this->resultJsonFactory->create();             
               return $result->setData([ "mensaje" => "Correcto" ]);                
             break;
             //VACIAR CARRITO
             case "15":
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();        
                $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');

                $quoteId = $checkoutSession->getQuote()->getId();
                $quoteModel = $objectManager->create('Magento\Quote\Model\Quote');
                $quoteItem = $quoteModel->load($quoteId);
                $quoteItem->delete();

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cartObject = $objectManager->create('Magento\Checkout\Model\Cart')->truncate();
                $cartObject->saveQuote();

                $result = $this->resultJsonFactory->create();             
                return $result->setData([ "mensaje" => "Correcto" ]);
             break;
             //RECORRER CARRITO Y VALIDAR EXISTENCIAS EN INVENTARIO COOPELESCA
             case "16":

              $om = \Magento\Framework\App\ObjectManager::getInstance();
              $cart = $om->get('\Magento\Checkout\Model\Cart');  
              $storeManager = $om->get('\Magento\Store\Model\StoreManagerInterface');
              $codigoAlmacen = $storeManager->getStore()->getCode();
              //Almacen e idioma
              $codigosAlmacenSeparados = explode("_", $codigoAlmacen);

              $itemsVisible = $cart->getQuote()->getAllItems();
              $compra = true;
              $productos = "";
              $precio ="";
              foreach($itemsVisible as $item) {
                  $productQty = $item["qty"];

                  $curl = curl_init();
                  $sumCantidad = '{"Sku":"'.$item->getSku().'","IdAlmacen":"'.$codigosAlmacenSeparados[0].'"}';
                  curl_setopt_array($curl, array(
                    CURLOPT_URL => $ws."Inventario/ConsultarArticulo",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 500,
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
                  
                  if(!$err){ 
                    $producto_data = json_decode($response);
      
                    if($producto_data != null ){
                        if( ($producto_data->CodigoRespuesta == "-200000")||((int)$producto_data->Articulo->Cantidad <= 0)||((int)$productQty > (int)$producto_data->Articulo->Cantidad) ){
                          $compra = false;
                          $productos .= " ".$item->getSku()." ";
                          break;                                                                                   
                        }                        
                     }
                  }else{
                    $this->_messageManager->addError($response);
                  } 
              }


              $result = $this->resultJsonFactory->create();             
              return $result->setData([ "mensaje" => $compra,"productos" => $productos, "precios" => $precio]);
             break;

            default:      
                return "";
                break;

          }
        }else{
          return false;
        }
       

       }
       public function iniciarSesion($cedula, $clave,$ws){

           $objectManager = ObjectManager::getInstance();
           $customer = ObjectManager::getInstance()->get(Customer::class)
                        ->getCollection()
                        ->addAttributeToFilter('cedula', $cedula)
                        ->getData();

           if( isset($customer)&&( count($customer) > 0 ) ) {

               $customer_id = $customer[0]["entity_id"];
               $password_hash = $customer[0]["password_hash"];
               $password_hash_parts = explode(":", $password_hash);

               $current_password = hash("sha256", ($password_hash_parts[1] . $clave));

               if($current_password == $password_hash_parts[0]){
                   $customer_by_id = $objectManager->create('\Magento\Customer\Model\Customer')->load($customer_id);
                   $customerSession = $objectManager->create('\Magento\Customer\Model\Session');
                   $customerSession->setCustomerAsLoggedIn($customer_by_id);
                   $customerSession->regenerateId();
                   return true;
               }else{
                return false;
               }

           }else{
            return false;
           }

       }
       public function EsAsociado($DatosAsociado,$ws){

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => $ws."Asociado/ConsultarEsAsociado",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $DatosAsociado,
            CURLOPT_HTTPHEADER => array(
              "Cache-Control: no-cache",
              "Content-Type: application/json"
            ),
          ));

          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if ($err) {
            return '{"error":"'.htmlentities($err).'"}';
          } else {
            return $response;
          }

      }
      public function GetAlmacenes($ws){

          $curl = curl_init();
              
          curl_setopt_array($curl, array(
            CURLOPT_URL => $ws."Almacen/ConsultarAlmacenes",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array("Cache-Control: no-cache", "Content-Type: application/json"),
          ));

          $response = curl_exec($curl);
          $err = curl_error($curl);

          curl_close($curl);

          if($err){
           return '{"error":"'.htmlentities($err).'"}';
          }else{
           return $response;
          }

      }
      public function GetDatosAsociado($cedula,$password,$ws){

        $credenciales = array('IdCuenta' => $cedula, 'Contrasena' => $password );
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $ws."Asociado/IniciarSesionAsociado",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 500,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($credenciales),
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",            
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return '{"error":"'.htmlentities($err).'"}';
        } else {
          return $response;
        }

      }
      public function GetMetodosEnvio($postdata,$ws){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $ws."Envio/ConsultarOpcionesEnvio",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 500,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $postdata,
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return '{"error":"'.htmlentities($err).'"}';
        } else {
          return $response;
        }

      }
    public function GetEstadoPedido($postdata,$ws){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ws."Pedido/ConsultarHistorialPedido",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return '{"error":"'.htmlentities($err).'"}';
        } else {
            return $response;
        }

    }
    public function GetCalculoCredito($postdata,$ws){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ws."Asociado/ConsultarCalculoFinanciamiento",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return '{"error":"'.htmlentities($err).'"}';
        } else {
            return $response;
        }

    }
    public function GetContratos($postdata,$ws){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ws."Asociado/ConsultarAsociado",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return '{"error":"'.htmlentities($err).'"}';
        } else {
            return $response;
        }

    }


}
