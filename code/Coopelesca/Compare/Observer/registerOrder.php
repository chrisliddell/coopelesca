<?php

namespace Coopelesca\Compare\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;


class registerOrder implements ObserverInterface{

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

	$order = $observer->getOrder();
 	$quote = $observer->getQuote();
    /*
	$curl = curl_init();
	$sumCantidad = '{"Sku":"'.$product->getSku().'","IdAlmacen":"'.$storeManager->getStore()->getCode().'"}';
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

	if(!$err){
		
	  $producto_data = json_decode($response);

	}else{
	  $this->_messageManager->addError($response);
	}*/

}

}