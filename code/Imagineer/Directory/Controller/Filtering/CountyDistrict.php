<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Imagineer\Directory\Controller\Filtering;

/**
 * Description of CountyDistrict
 *
 * @author root
 */

use Magento\Framework\App\Action\Action;

class CountyDistrict extends Action {    
    public function execute() {
        $arrRes = [];
        
        $countyId = $this->getRequest()->getParam('county');
        if (!empty($countyId)) {
           $arrDistricts = $this->_objectManager->create(
                \Imagineer\Directory\Model\ResourceModel\District\Collection::class
            )->addCountyFilter(
                $countyId
            )->load()->toOptionIdArray();
           
           if (!empty($arrDistricts)) {
                foreach ($arrDistricts as $district) {
                    $arrRes[] = $district;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }

}
