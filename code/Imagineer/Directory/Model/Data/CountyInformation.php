<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Model\Data;

/**
 * Class Region Information
 *
 * @codeCoverageIgnore
 */
class CountyInformation extends \Magento\Framework\Api\AbstractExtensibleObject implements \Imagineer\Directory\Api\Data\CountyInformationInterface
{

    const KEY_COUNTY_ID = 'county_id';

    const KEY_COUNTY_CODE = 'county_code';

    const KEY_COUNTY_NAME = 'county_name';

    /**
     *
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_get(self::KEY_COUNTY_ID);
    }

    /**
     *
     * @inheritDoc
     */
    public function setId($countyId)
    {
        $this->setData(self::KEY_COUNTY_ID, $countyId);
    }

    /**
     *
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->_get(self::KEY_COUNTY_CODE);
    }

    /**
     *
     * @inheritDoc
     */
    public function setCode($countyCode)
    {
        $this->setData(self::KEY_COUNTY_CODE, $countyCode);
    }

    /**
     *
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_get(self::KEY_COUNTY_NAME);
    }

    /**
     *
     * @inheritDoc
     */
    public function setName($countyName)
    {
        $this->setData(self::KEY_COUNTY_NAME, $countyName);
    }

    /**
     *
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     *
     * @inheritDoc
     */
    public function setExtensionAttributes(\Imagineer\Directory\Api\Data\CountyInformationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
