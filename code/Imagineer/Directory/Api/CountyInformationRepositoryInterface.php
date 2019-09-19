<?php
namespace Imagineer\Directory\Api;


interface CountyInformationRepositoryInterface
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
     * @return \Imagineer\Directory\Api\Data\CountyInformationInterface
     */
    public function loadByCode($code);

    /**
     * Load county by id
     *     
     * @param string $countyId
     * @return \Imagineer\Directory\Api\Data\CountyInformationInterface
     */
    public function loadById($countyId);

    /**
     * Load county by name
     *
     * @param string $name
     * @param string $regionId
     * @return \Imagineer\Directory\Api\Data\CountyInformationInterface
     */
    public function loadByName($name, $regionId);    

}