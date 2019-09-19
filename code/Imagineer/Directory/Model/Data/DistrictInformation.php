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
class DistrictInformation extends \Magento\Framework\Api\AbstractExtensibleObject implements \Imagineer\Directory\Api\Data\DistrictInformationInterface
{

    const KEY_DISTRICT_ID = 'district_id';

    const KEY_DISTRICT_CODE = 'district_code';

    const KEY_DISTRICT_NAME = 'district_name';

    /**
     *
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_get(self::KEY_DISTRICT_ID);
    }

    /**
     *
     * @inheritDoc
     */
    public function setId($districtId)
    {
        $this->setData(self::KEY_DISTRICT_ID, $districtId);
    }

    /**
     *
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->_get(self::KEY_DISTRICT_CODE);
    }

    /**
     *
     * @inheritDoc
     */
    public function setCode($districtCode)
    {
        $this->setData(self::KEY_DISTRICT_CODE, $districtCode);
    }

    /**
     *
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_get(self::KEY_DISTRICT_NAME);
    }

    /**
     *
     * @inheritDoc
     */
    public function setName($districtName)
    {
        $this->setData(self::KEY_DISTRICT_NAME, $districtName);
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
    public function setExtensionAttributes(\Imagineer\Directory\Api\Data\DistrictInformationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
