<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Imagineer\Directory\Controller\Filtering;

/**
 * Description of DistrictTown
 *
 * @author root
 */

use Magento\Framework\App\Action\Action;

class DistrictTown extends Action {    
    public function execute() {
        $arrRes = [];
        
        $districtId = $this->getRequest()->getParam('district');
        if (!empty($districtId)) {
           $arrTowns = $this->_objectManager->create(
                \Imagineer\Directory\Model\ResourceModel\Town\Collection::class
            )->addDistrictFilter(
                $districtId
            )->load()->toOptionIdArray();
           
           if (!empty($arrTowns)) {
                foreach ($arrTowns as $town) {
                    $arrRes[] = $town;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }
}
