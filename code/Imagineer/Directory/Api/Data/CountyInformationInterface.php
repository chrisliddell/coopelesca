<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Api\Data;

/**
 * County Information interface.
 *
 * @api
 * @since 0.9.0
 */
interface CountyInformationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get county id
     *
     * @return string
     */
    public function getId();

    /**
     * Set county id
     *
     * @param string $countyId
     * @return $this
     */
    public function setId($countyId);


    /**
     * Get county code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set county code
     *
     * @param string $countyCode
     * @return $this
     */
    public function setCode($countyCode);

    /**
     * Get county name
     *
     * @return string
     */
    public function getName();

    /**
     * Set county name
     *
     * @param string $countyName
     * @return $this
     */
    public function setName($countyName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Imagineer\Directory\Api\Data\CountyInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Imagineer\Directory\Api\Data\CountyInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Imagineer\Directory\Api\Data\CountyInformationExtensionInterface $extensionAttributes
    );
}
