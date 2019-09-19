<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Api\Data;

/**
 * Town Information interface.
 *
 * @api
 * @since 0.9.0
 */
interface TownInformationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get town id
     *
     * @return string
     */
    public function getId();

    /**
     * Set town id
     *
     * @param string $townId
     * @return $this
     */
    public function setId($townId);

    /**
     * Get town code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set town code
     *
     * @param string $townCode
     * @return $this
     */
    public function setCode($townCode);

    /**
     * Get town name
     *
     * @return string
     */
    public function getName();

    /**
     * Set town name
     *
     * @param string $townName
     * @return $this
     */
    public function setName($townName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Imagineer\Directory\Api\Data\TownInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Imagineer\Directory\Api\Data\TownInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Imagineer\Directory\Api\Data\TownInformationExtensionInterface $extensionAttributes
    );
}
