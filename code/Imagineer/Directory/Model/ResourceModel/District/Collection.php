<?php
namespace Imagineer\Directory\Model\ResourceModel\District;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'district_id';

    /**
     * Locale District name table name
     *
     * @var string
     */
    protected $_districtNameTable;

    /**
     * County table name
     *
     * @var string
     */
    protected $_countyTable;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
        ) {
            $this->_localeResolver = $localeResolver;
            $this->_resource = $resource;
            parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Imagineer\Directory\Model\District', 'Imagineer\Directory\Model\ResourceModel\District');

        $this->_regionTable = $this->getTable('directory_country_region');
        $this->_districtNameTable = $this->getTable('directory_county_district_name');

        $this->addOrder('name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
        $this->addOrder('default_name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $locale = $this->_localeResolver->getLocale();

        $this->addBindParam(':region_locale', $locale);
        $this->getSelect()->joinLeft(
            ['rname' => $this->_districtNameTable],
            'main_table.district_id = rname.district_id AND rname.locale = :region_locale',
            ['name']
            );

        return $this;
    }

    /**
     * Filter by county_id
     *
     * @param string|array $countyId
     * @return $this
     */
    public function addCountyFilter($countyId)
    {
        if (!empty($countyId)) {
            if (is_array($countyId)) {
                $this->addFieldToFilter('main_table.county_id', ['in' => $countyId]);
            } else {
                $this->addFieldToFilter('main_table.county_id', $countyId);
            }
        }
        return $this;
    }

    /**
     * Filter by county code
     *
     * @param string $countyCode
     * @return $this
     */
    public function addCountyCodeFilter($countyCode)
    {
        $this->getSelect()->joinLeft(
            ['county' => $this->_countyTable],
            'main_table.county_id = county.county_id'
            )->where(
                'county.code = ?',
                $countyCode
                );

            return $this;
    }

    /**
     * Filter by District code
     *
     * @param string|array $districtCode
     * @return $this
     */
    public function addDistrictCodeFilter($districtCode)
    {
        if (!empty($districtCode)) {
            if (is_array($districtCode)) {
                $this->addFieldToFilter('main_table.code', ['in' => $districtCode]);
            } else {
                $this->addFieldToFilter('main_table.code', $districtCode);
            }
        }
        return $this;
    }

    /**
     * Filter by district name
     *
     * @param string|array $districtName
     * @return $this
     */
    public function addDistrictNameFilter($districtName)
    {
        if (!empty($districtName)) {
            if (is_array($districtName)) {
                $this->addFieldToFilter('main_table.default_name', ['in' => $districtName]);
            } else {
                $this->addFieldToFilter('main_table.default_name', $districtName);
            }
        }
        return $this;
    }

    /**
     * Filter district by its code or name
     *
     * @param string|array $district
     * @return $this
     */
    public function addDistrictCodeOrNameFilter($district)
    {
        if (!empty($district)) {
            $condition = is_array($district) ? ['in' => $district] : $district;
            $this->addFieldToFilter(
                ['main_table.code', 'main_table.default_name'],
                [$condition, $condition]
                );
        }
        return $this;
    }

    /**
     * OptionArray for records in district
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = [];
        foreach ($this as $item) {
            $data['value'] = $item->getData('district_id');
            $data['label'] = $item->getData('default_name');
            $data['code'] = $item->getData('code');

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Loads Item By Id
     *
     * @param string $countyId
     * @return \Imagineer\Directory\Model\ResourceModel\District|null
     */
    public function getItemById($districtId)
    {
        foreach ($this->_items as $district) {
            if ($district->getDistrictId() == $districtId) {
                return $district;
            }
        }
        return null;
    }
}
