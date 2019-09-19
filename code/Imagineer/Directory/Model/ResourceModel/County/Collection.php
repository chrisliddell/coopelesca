<?php
namespace Imagineer\Directory\Model\ResourceModel\County;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'county_id';

    /**
     * Locale County name table name
     *
     * @var string
     */
    protected $_countyNameTable;

    /**
     * Region table name
     *
     * @var string
     */
    protected $_regionTable;

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
        $this->_init('Imagineer\Directory\Model\County', 'Imagineer\Directory\Model\ResourceModel\County');

        $this->_regionTable = $this->getTable('directory_country_region');
        $this->_countyNameTable = $this->getTable('directory_region_county_name');

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
            ['rname' => $this->_countyNameTable],
            'main_table.county_id = rname.county_id AND rname.locale = :region_locale',
            ['name']
            );

        return $this;
    }

    /**
     * Filter by region_id
     *
     * @param string|array $regionId
     * @return $this
     */
    public function addRegionFilter($regionId)
    {
        if (!empty($regionId)) {
            if (is_array($regionId)) {
                $this->addFieldToFilter('main_table.region_id', ['in' => $regionId]);
            } else {
                $this->addFieldToFilter('main_table.region_id', $regionId);
            }
        }
        return $this;
    }

    /**
     * Filter by region code
     *
     * @param string $regionCode
     * @return $this
     */
    public function addRegionCodeFilter($regionCode)
    {
        $this->getSelect()->joinLeft(
            ['region' => $this->_regionTable],
            'main_table.region_id = region.region_id'
            )->where(
                'region.code = ?',
                $regionCode
                );

            return $this;
    }

    /**
     * Filter by County code
     *
     * @param string|array $countyCode
     * @return $this
     */
    public function addCountyCodeFilter($countyCode)
    {
        if (!empty($countyCode)) {
            if (is_array($countyCode)) {
                $this->addFieldToFilter('main_table.code', ['in' => $countyCode]);
            } else {
                $this->addFieldToFilter('main_table.code', $countyCode);
            }
        }
        return $this;
    }

    /**
     * Filter by county name
     *
     * @param string|array $countyName
     * @return $this
     */
    public function addCountyNameFilter($countyName)
    {
        if (!empty($countyName)) {
            if (is_array($countyName)) {
                $this->addFieldToFilter('main_table.default_name', ['in' => $countyName]);
            } else {
                $this->addFieldToFilter('main_table.default_name', $countyName);
            }
        }
        return $this;
    }

    /**
     * Filter county by its code or name
     *
     * @param string|array $county
     * @return $this
     */
    public function addCountyCodeOrNameFilter($county)
    {
        if (!empty($county)) {
            $condition = is_array($county) ? ['in' => $county] : $county;
            $this->addFieldToFilter(
                ['main_table.code', 'main_table.default_name'],
                [$condition, $condition]
                );
        }
        return $this;
    }

    /**
     * OptionArray for records in County
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = [];
        foreach ($this as $item) {
            $data['value'] = $item->getData('county_id');
            $data['label'] = $item->getData('default_name');

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Loads Item By Id
     *
     * @param string $countyId
     * @return \Imagineer\Directory\Model\ResourceModel\County|null
     */
    public function getItemById($countyId)
    {
        foreach ($this->_items as $county) {
            if ($county->getCountyId() == $countyId) {
                return $county;
            }
        }
        return null;
    }
}

