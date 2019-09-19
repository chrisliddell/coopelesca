<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Api\Data;

/**
 * District Information interface.
 *
 * @api
 * @since 0.9.0
 */
interface DistrictInformationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get district id
     *
     * @return string
     */
    public function getId();

    /**
     * Set district id
     *
     * @param string $districtId
     * @return $this
     */
    public function setId($districtId);

    /**
     * Get district code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set district code
     *
     * @param string $districtCode
     * @return $this
     */
    public function setCode($districtCode);

    /**
     * Get district name
     *
     * @return string
     */
    public function getName();

    /**
     * Set district name
     *
     * @param string $districtName
     * @return $this
     */
    public function setName($districtName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Imagineer\Directory\Api\Data\DistrictInformationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Imagineer\Directory\Api\Data\DistrictInformationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Imagineer\Directory\Api\Data\DistrictInformationExtensionInterface $extensionAttributes
    );
}
