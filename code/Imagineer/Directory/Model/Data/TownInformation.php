<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Model\Data;

/**
 * Class Town Information
 *
 * @codeCoverageIgnore
 */
class TownInformation extends \Magento\Framework\Api\AbstractExtensibleObject implements \Imagineer\Directory\Api\Data\TownInformationInterface
{

    const KEY_TOWN_ID = 'town_id';

    const KEY_TOWN_CODE = 'town_code';

    const KEY_TOWN_NAME = 'town_name';

    /**
     *
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_get(self::KEY_TOWN_ID);
    }

    /**
     *
     * @inheritDoc
     */
    public function setId($townId)
    {
        $this->setData(self::KEY_TOWN_ID, $townId);
    }

    /**
     *
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->_get(self::KEY_TOWN_CODE);
    }

    /**
     *
     * @inheritDoc
     */
    public function setCode($townCode)
    {
        $this->setData(self::KEY_TOWN_CODE, $townCode);
    }

    /**
     *
     * @inheritDoc
     */
    public function getName()
    {
        return $this->_get(self::KEY_TOWN_NAME);
    }

    /**
     *
     * @inheritDoc
     */
    public function setName($townName)
    {
        $this->setData(self::KEY_TOWN_NAME, $townName);
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
    public function setExtensionAttributes(\Imagineer\Directory\Api\Data\TownInformationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
