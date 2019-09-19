<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Directory data helper
 *
 * @since 0.9.0
 */
class Data extends \Magento\Directory\Helper\Data
{
    /*
     * Path to config value, which lists counties, for which region is required.
     */
    const XML_PATH_COUNTIES_REQUIRED = 'general/county/county_required';
    
    /*
     * Path to config value, which detects whether or not display the county for the region, if it is not required
     */
    const XML_PATH_DISPLAY_ALL_COUNTIES = 'general/county/display_all';
    
    /*
     * Path to config value, which lists districts, for which county is required.
     */
    const XML_PATH_DISTRICTS_REQUIRED = 'general/district/district_required';
    
    /*
     * Path to config value, which detects whether or not display the district for the county, if it is not required
     */
    const XML_PATH_DISPLAY_ALL_DISTRICTS = 'general/district/display_all';

    /*
     * Path to config value, which lists towns, for which district is required.
     */
    const XML_PATH_TOWNS_REQUIRED = 'general/town/town_required';

    /*
     * Path to config value, which detects whether or not display the town for the district, if it is not required
     */
    const XML_PATH_DISPLAY_ALL_TOWNS = 'general/town/display_all';
    
    /**
     * County collection
     *
     * @var \Imagineer\Directory\Model\ResourceModel\County\Collection
     */
    protected $_countyCollection;

    /**
     * District collection
     *
     * @var \Imagineer\Directory\Model\ResourceModel\District\Collection
     */
    protected $_districtCollection;

    /**
     * Json representation of counties data
     *
     * @var string
     */
    protected $_countyJson;
    
    /**
     * Json representation of districts data
     *
     * @var string
     */
    protected $_districtJson;

    /**
     * Json representation of districts data
     *
     * @var string
     */
    protected $_townJson;
    
    /**
     * ISO2 country codes which have optional Zip/Postal pre-configured
     *
     * @var array
     */
    protected $_optZipCountries = null;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     *
     * @var \Imagineer\Directory\Model\ResourceModel\County\CollectionFactory
     */
    protected $_countyCollectionFactory;
    
    /**
     *
     * @var \Imagineer\Directory\Model\ResourceModel\District\CollectionFactory 
     */
    protected $_districtCollectionFactory;

    /**
     *
     * @var \Imagineer\Directory\Model\ResourceModel\Town\CollectionFactory
     */
    protected $_townCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory,
     * @param \Imagineer\Directory\Model\ResourceModel\County\CollectionFactory $countyCollectionFactory,
     * @param \Imagineer\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
     * @param \Imagineer\Directory\Model\ResourceModel\Town\CollectionFactory $townCollectionFactory,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Framework\App\Cache\Type\Config $configCacheType, 
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection, 
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory,
        \Imagineer\Directory\Model\ResourceModel\County\CollectionFactory $countyCollectionFactory,
        \Imagineer\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Imagineer\Directory\Model\ResourceModel\Town\CollectionFactory $townCollectionFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper, 
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Directory\Model\CurrencyFactory $currencyFactory)
    {
        parent::__construct($context, 
            $configCacheType, $countryCollection, $regCollectionFactory, $jsonHelper, $storeManager, $currencyFactory);
        $this->_countyCollectionFactory = $countyCollectionFactory;
        $this->_districtCollectionFactory = $districtCollectionFactory;
        $this->_townCollectionFactory = $townCollectionFactory;
    }

    /**
     * Retrieve county collection
     *
     * @return \Imagineer\Directory\Model\ResourceModel\County\Collection
     */
    public function getCountyCollection()
    {
        if (! $this->_countyCollection) {
            $this->_countyCollection = $this->_countyCollectionFactory->create();
            $this->_countyCollection->addCountyFilter($this->getCountyData()->getCountyId())->load();
        }
        return $this->_countyCollection;
    }
    
    /**
     * Retrieve district collection
     *
     * @return \Imagineer\Directory\Model\ResourceModel\District\Collection
     */
    public function getDistrictCollection()
    {
        if (! $this->_districtCollection) {
            $this->_districtCollection = $this->_districtCollectionFactory->create();
            $this->_districtCollection->addDistrictFilter($this->getAddress()
                ->getDistrictId())
                ->load();
        }
        return $this->_districtCollection;
    }

    /**
     * Retrieve town collection
     *
     * @return \Imagineer\Directory\Model\ResourceModel\Town\Collection
     */
    public function getTownCollection()
    {
        if (!$this->_townCollection) {
            $this->_townCollection = $this->_townCollectionFactory->create();
            $this->_townCollection->addTownFilter($this->getTownData()->getDistrictId())
                ->load();
        }
        return $this->_townCollection;
    }

    /**
     * Retrieve counties data json
     *
     * @return string
     */
    public function getCountyJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, [
            'group' => 'TEST',
            'method' => __METHOD__
        ]);
        if (! $this->_countyJson) {
            $cacheKey = 'DIRECTORY_COUNTIES_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $counties = $this->getCountyData();
                $json = $this->jsonHelper->jsonEncode($counties);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_countyJson = $json;
        }
        
        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);
        return $this->_countyJson;
    }
    
    /**
     * Retrieve districts data json
     *
     * @return string
     */
    public function getDistrictJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, [
            'group' => 'TEST',
            'method' => __METHOD__
        ]);
        if (! $this->_districtJson) {
            $cacheKey = 'DIRECTORY_DISTRICTS_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $districts = $this->getDistrictData();
                $json = $this->jsonHelper->jsonEncode($districts);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_districtJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);
        return $this->_districtJson;
    }

    /**
     * Retrieve towns data json
     *
     * @return string
     */
    public function getTownJson()
    {
        \Magento\Framework\Profiler::start('TEST: ' . __METHOD__, [
            'group' => 'TEST',
            'method' => __METHOD__
        ]);
        if (! $this->_townJson) {
            $cacheKey = 'DIRECTORY_TOWN_JSON_STORE' . $this->_storeManager->getStore()->getId();
            $json = $this->_configCacheType->load($cacheKey);
            if (empty($json)) {
                $districts = $this->getTownData();
                $json = $this->jsonHelper->jsonEncode($districts);
                if ($json === false) {
                    $json = 'false';
                }
                $this->_configCacheType->save($json, $cacheKey);
            }
            $this->_townJson = $json;
        }

        \Magento\Framework\Profiler::stop('TEST: ' . __METHOD__);
        return $this->_townJson;
    }

    /**
     * Returns the list of regions, for which a county is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getRegionsWithRequiredCounties($asJson = false)
    {
        $value = trim(
            $this->scopeConfig->getValue(
                self::XML_PATH_COUNTY_REQUIRED,
                ScopeInterface::SCOPE_STORE
            )
        );
        $regionList = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        if ($asJson) {
            return $this->jsonHelper->jsonEncode($regionList);
        }
        return $regionList;
    }

    /**
     * Returns the list of counties, for which a district is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getCountiesWithRequiredDistricts($asJson = false)
    {
        $value = trim(
            $this->scopeConfig->getValue(
                self::XML_PATH_DISTRICT_REQUIRED,
                ScopeInterface::SCOPE_STORE
            )
        );
        $countyList = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        if ($asJson) {
            return $this->jsonHelper->jsonEncode($countyList);
        }
        return $countyList;
    }

    /**
     * Returns the list of districts, for which a town is required
     *
     * @param boolean $asJson
     * @return array
     */
    public function getDistrictsWithRequiredTowns($asJson = false)
    {
        $value = trim(
            $this->scopeConfig->getValue(
                self::XML_PATH_TOWN_REQUIRED,
                ScopeInterface::SCOPE_STORE
            )
        );
        $townList = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        if ($asJson) {
            return $this->jsonHelper->jsonEncode($townList);
        }
        return $townList;
    }

    /**
     * Return, whether non-required county should be shown
     *
     * @return bool
     */
    public function isShowNonRequiredCounty()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_DISPLAY_ALL_COUNTIES, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return, whether non-required districts should be shown
     *
     * @return bool
     */
    public function isShowNonRequiredDistrict()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_DISPLAY_ALL_DISTRICTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return, whether non-required towns should be shown
     *
     * @return bool
     */
    public function isShowNonRequiredTown()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_DISPLAY_ALL_TOWNS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns flag, which indicates whether county is required for specified region
     *
     * @param string $regionId
     * @return bool
     */
    public function isCountyRequired($regionId)
    {
        $countyList = $this->getRegionsWithRequiredCounties();
        if (!is_array($countyList)) {
            return false;
        }
        return in_array($regionId, $countyList);
    }

    /**
     * Returns flag, which indicates whether district is required for specified county
     *
     * @param string $countyId
     * @return bool
     */
    public function isDistrictRequired($countyId)
    {
        $districtList = $this->getCountiesWithRequiredDistricts();
        if (!is_array($districtList)) {
            return false;
        }
        return in_array($countyId, $districtList);
    }

    /**
     * Returns flag, which indicates whether town is required for specified district
     *
     * @param string $districtId
     * @return bool
     */
    public function isTownRequired($districtId)
    {
        $townList = $this->getDistrictsWithRequiredTowns();
        if (!is_array($townList)) {
            return false;
        }
        return in_array($districtId, $townList);
    }

    /**
     * 
     * Retrieve counties data
     *
     * @return array
     */
    public function getCountyData()
    {
        $countyIds = [];
        foreach ($this->getCountyCollection() as $county) {
            $countyIds[] = $county->getCountyId();
        }
        $collection = $this->_countyCollectionFactory->create();
        $collection->addCountyFilter($countyIds)->load();
        $counties = [
            'config' => [
                'show_all_counties' => $this->isShowNonRequiredCounty(),
                'counties_required' => $this->getRegionsWithRequiredCounties()
            ]
        ];
        foreach ($collection as $county) {
            /** @var $county \Imagineer\Directory\Model\County */
            if (! $county->getCountyId()) {
                continue;
            }
            $counties[$county->getCountyId()][$county->getCountyId()] = [
                'code' => $county->getCode(),
                'name' => (string) __($county->getName())
            ];
        }
        return $counties;
    }

    /**
     * 
     * Retrieve district data
     *
     * @return array
     */
    public function getDistrictData()
    {
        $districtIds = [];
        foreach ($this->getDistrictCollection() as $district) {
            $districtIds[] = $district->getDistrictId();
        }
        $collection = $this->_districtCollectionFactory->create();
        $collection->addDistrictFilter($districtIds)->load();
        $districts = [
            'config' => [
                'show_all_districts' => $this->isShowNonRequiredDistrict(),
                'districts_required' => $this->getCountiesWithRequiredDistricts()
            ]
        ];
        foreach ($collection as $district) {
            /** @var $district \Imagineer\Directory\Model\District */
            if (! $district->getDistrictId()) {
                continue;
            }
            $districts[$district->getDistrictId()][$district->getDistrictId()] = [
                'code' => $district->getCode(),
                'name' => (string) __($district->getName())
            ];
        }
        return $districts;
    }

    /**
     *
     * Retrieve town data
     *
     * @return array
     */
    public function getTownData()
    {
        $townIds = [];
        foreach ($this->getTownCollection() as $town) {
            $townIds[] = $town->getTownId();
        }
        $collection = $this->_townCollectionFactory->create();
        $collection->addTownFilter($townIds)->load();
        $towns = [
            'config' => [
                'show_all_towns' => $this->isShowNonRequiredTown(),
                'towns_required' => $this->getDistrictsWithRequiredTowns()
            ]
        ];
        foreach ($collection as $town) {
            /** @var $town \Imagineer\Directory\Model\Town */
            if (!$town->getTownId()) {
                continue;
            }
            $towns[$town->getTownId()][$town->getTownId()] = [
                'code' => $town->getCode(),
                'name' => (string)__($town->getName())
            ];
        }
        return $towns;
    }

    /**
     * Retrieve region collection
     *
     * @return \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = $this->_regCollectionFactory->create();
            $this->_regionCollection->addCountryFilter($this->getCountryCollection()->getAllIds())->load();
        }
        return $this->_regionCollection;
    }
    
    /**
     * Retrieve list of codes of the most used countries
     *
     * @return array
     */
    /** public function getTopCountryCodes()
    {
        $configValue = (string) $this->scopeConfig->getValue(self::XML_PATH_TOP_COUNTRIES, ScopeInterface::SCOPE_STORE);
        return ! empty($configValue) ? explode(',', $configValue) : [];
    }
     */

    /**
     * Retrieve weight unit
     *
     * @return string
     */
    /**
    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_WEIGHT_UNIT, ScopeInterface::SCOPE_STORE);
    }
     */
}
