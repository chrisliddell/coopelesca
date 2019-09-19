<?php

namespace Imagineer\Address\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $fieldsetConfig;

    protected $logger;

    public function __construct(
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->fieldsetConfig = $fieldsetConfig;
        $this->logger = $logger;
    }

    public function getExtraCheckoutAddressFields($fieldset='extra_checkout_billing_address_fields',$root='global'){

        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);

        $extraCheckoutFields = [];

        foreach($fields as $field=>$fieldInfo){
            $extraCheckoutFields[] = $field;
        }

        return $extraCheckoutFields;

    }
    
    public function transportFieldsFromExtensionAttributesToObject(
        $fromObject,
        $toObject,
        $fieldset='extra_checkout_billing_address_fields'
    )
    {
        foreach($this->getExtraCheckoutAddressFields($fieldset) as $extraField) {

            $set = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));
            $get = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $extraField)));

            $value = $fromObject->$get();
            try {
                $toObject->$set($value);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $toObject;
    }
}