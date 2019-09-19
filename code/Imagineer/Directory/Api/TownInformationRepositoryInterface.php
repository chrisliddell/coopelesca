<?php
namespace Imagineer\Directory\Api;


interface TownInformationRepositoryInterface
{
    /**
     * Retrieve county name
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
     * @return \Imagineer\Directory\Api\Data\TownInformationInterface
     */
    public function loadByCode($code);

    /**
     * Load county by id
     *
     * @param string $districtId
     * @return \Imagineer\Directory\Api\Data\TownInformationInterface
     */
    public function loadById($townId);

    /**
     * Load county by name
     *
     * @param string $name
     * @param string $townId
     * @return \Imagineer\Directory\Api\Data\TownInformationInterface
     */
    public function loadByName($name, $townId);

}