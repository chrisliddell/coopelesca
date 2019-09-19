<?php
namespace Imagineer\Directory\Api;


interface DistrictInformationRepositoryInterface
{
    /**
     * Retrieve district name
     *
     * If name is not declared, then default_name is used
     *
     * @return string
     */
	public function getName();

    /**
     * Load county by code
     *
     * @param string $code     
     * @return \Imagineer\Directory\Api\Data\DistrictInformationInterface
     */
    public function loadByCode($code);

    /**
     * Load district by id
     *
     * @param string $districtId
     * @return \Imagineer\Directory\Api\Data\DistrictInformationInterface
     */
    public function loadById($districtId);

    /**
     * Load district by name
     *
     * @param string $name
     * @param string $districtId
     * @return \Imagineer\Directory\Api\Data\DistrictInformationInterface
     */
    public function loadByName($name, $districtId);

}