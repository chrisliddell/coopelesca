<?php

namespace Coopelesca\Compare\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class removeCart implements ObserverInterface
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
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        // Obtener el ID desde el detalle del producto
        $productId = (int)$observer->getEvent()->getQuoteItem()->getId();
        
        //Obtener sesión actual del carrito
        //$checkoutManager = $this->objectManager->get('\Magento\Checkout\Model\Session');
        //$metodosEnvio = $checkoutManager->getMetodosEnvioCoopelesca();
        //$clave = array_search($productId, $metodosEnvio);
    }
}