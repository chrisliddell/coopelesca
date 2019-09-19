<?php
namespace Imagineer\Directory\Controller\Filtering;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegionCounty
 *
 * @author root
 */

use Magento\Framework\App\Action\Action;

class RegionCounty extends Action {

    public function execute() {
        $arrRes = [];
        
        $regionId = $this->getRequest()->getParam('region');
        if (!empty($regionId)) {
           $arrCounties = $this->_objectManager->create(
                \Imagineer\Directory\Model\ResourceModel\County\Collection::class
            )->addRegionFilter(
                $regionId
            )->load()->toOptionIdArray();
           
           if (!empty($arrCounties)) {
                foreach ($arrCounties as $county) {
                    $arrRes[] = $county;
                }
            }        
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($arrRes)
        );
    }

}