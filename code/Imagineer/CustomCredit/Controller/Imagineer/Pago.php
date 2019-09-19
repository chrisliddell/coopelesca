<?php

namespace Imagineer\CustomCredit\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\Framework\App\ObjectManager;


class Pago extends  \Magento\Framework\App\Action\Action {


        public function __construct(
               Context  $context,
               \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
        ){            
            $this->resultJsonFactory = $resultJsonFactory;
            parent::__construct($context);
        }


        public function execute() {         
        }

}