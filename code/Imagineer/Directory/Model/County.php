<?php

/**
 * Copyright © Imagineer. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Model;

/**
 * Description of County
 *
 * @method string getRegionId()
 * @method string getCountyId()
 * @method \Imagineer\Directory\Model\County setCountyId(string $value)
 * @method string getCode()
 * @method \Imagineer\Directory\Model\County setCode(string $value)
 * @method string getDefaultName()
 * @method \Imagineer\Directory\Model\County setDefaultName(string $value)
 *        
 * @author Jose Luis Moya <jose@smartnow.tech>
 *        
 * @api
 * @since 1.0.0
 *       
 */
class County extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Imagineer\Directory\Model\ResourceModel\County::class);
    }

    /**
     * Retrieve county name
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
     * Load county by id
     *     
     * @param string $countyId
     * @return $this
     */
    public function loadById($countyId)
    {
        if ($countyId) {
            $this->_getResource()->loadById($this, $countyId);
        }
        return $this;
    }

    /**
     * Load county by code
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
     * Load county by name
     *
     * @param string $name
     * @param string $regionId
     * @return $this
     */
    public function loadByName($name, $regionId)
    {
        $this->_getResource()->loadByName($this, $name, $regionId);
        return $this;
    }
}
