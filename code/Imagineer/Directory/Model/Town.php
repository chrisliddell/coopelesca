<?php

/*
 * Copyright © Imagineer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Imagineer\Directory\Model;

/**
 * Description of Town
 *
 * @author Jose Luis Moya <jose@smartnow.tech>
 *
 * @method string getCountyId()
 * @method string getDistrictId()
 * @method string getTownId()
 * @method \Imagineer\Directory\Model\Town setDistrictId(string $value)
 * @method string getCode()
 * @method \Imagineer\Directory\Model\Town setCode(string $value)
 * @method string getDefaultName()
 * @method \Imagineer\Directory\Model\Town setDefaultName(string $value)
 *
 * @api
 * @since 1.0.0
 */
class Town extends \Magento\Framework\Model\AbstractModel {
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Imagineer\Directory\Model\ResourceModel\Town::class);
    }

    /**
     * Retrieve town name
     *
     * If name is not declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        if ($name === null) {
            $name = $this->getData('default_name');
        }
        return $name;
    }

    /**
     * Load district by id
     *
     * @param string $districtId
     * @return $this
     */
    public function loadById($districtId)
    {
        if ($districtId) {
            $this->_getResource()->loadById($this, $districtId);
        }
        return $this;
    }

    /**
     * Load town by code
     *     
     * @param string $code
     * @return $this
     */
    public function loadByCode($code)
    {
        if ($code) {
            $this->_getResource()->loadByCode($this, $code);
        }
        return $this;
    }

    /**
     * Load district by name
     *
     * @param string $name
     * @param string $countyId
     * @return $this
     */
    public function loadByName($name, $countyId)
    {
        $this->_getResource()->loadByName($this, $name, $countyId);
        return $this;
    }
}
