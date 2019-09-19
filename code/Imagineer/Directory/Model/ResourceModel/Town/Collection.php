<?php
namespace Imagineer\Directory\Model\ResourceModel\Town;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     *
     * @var string
     */
    protected $_idFieldName = 'town_id';

    /**
     * Locale Town name table name
     *
     * @var string
     */
    protected $_townNameTable;

    /**
     * District table name
     *
     * @var string
     */
    protected $_districtTable;

    /**
     *
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(\Magento\Framework\Data\Collection\EntityFactory $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Framework\DB\Adapter\AdapterInterface $connection = null, \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
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
        $this->_init('Imagineer\Directory\Model\Town', 'Imagineer\Directory\Model\ResourceModel\Town');

        $this->_districtTable = $this->getTable('directory_country_region');
        $this->_townNameTable = $this->getTable('directory_district_town_name');

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
        $this->getSelect()->joinLeft([
            'rname' => $this->_townNameTable
        ], 'main_table.town_id = rname.town_id AND rname.locale = :region_locale', [
            'name'
        ]);

        return $this;
    }

    /**
     * Filter by district_id
     *
     * @param string|array $districtId
     * @return $this
     */
    public function addDistrictFilter($districtId)
    {
        if (! empty($districtId)) {
            if (is_array($districtId)) {
                $this->addFieldToFilter('main_table.district_id', ['in' => $districtId]);
            } else {
                $this->addFieldToFilter('main_table.district_id', $districtId);
            }
        }
        return $this;
    }

    /**
     * Filter by district code
     *
     * @param string $districtCode
     * @return $this
     */
    public function addDistrictCodeFilter($districtCode)
    {
        $this->getSelect()
            ->joinLeft([
            'district' => $this->_districtTable
        ], 'main_table.district_id = district.district_id')
            ->where('district.code = ?', $districtCode);

        return $this;
    }

    /**
     * Filter by Town code
     *
     * @param string|array $townCode
     * @return $this
     */
    public function addTownCodeFilter($townCode)
    {
        if (! empty($townCode)) {
            if (is_array($townCode)) {
                $this->addFieldToFilter('main_table.code', [
                    'in' => $townCode
                ]);
            } else {
                $this->addFieldToFilter('main_table.code', $townCode);
            }
        }
        return $this;
    }

    /**
     * Filter by town name
     *
     * @param string|array $townName
     * @return $this
     */
    public function addDistrictNameFilter($townName)
    {
        if (! empty($townName)) {
            if (is_array($townName)) {
                $this->addFieldToFilter('main_table.default_name', [
                    'in' => $townName
                ]);
            } else {
                $this->addFieldToFilter('main_table.default_name', $townName);
            }
        }
        return $this;
    }

    /**
     * Filter town by its code or name
     *
     * @param string|array $town
     * @return $this
     */
    public function addTownCodeOrNameFilter($town)
    {
        if (! empty($town)) {
            $condition = is_array($town) ? [
                'in' => $town
            ] : $town;
            $this->addFieldToFilter([
                'main_table.code',
                'main_table.default_name'
            ], [
                $condition,
                $condition
            ]);
        }
        return $this;
    }

    /**
     * OptionArray for records in town
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = [];
        foreach ($this as $item) {
            $data['value'] = $item->getData('town_id');
            $data['label'] = $item->getData('default_name');
            $data['code'] = $item->getData('code');

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Loads Item By Id
     *
     * @param string $townId
     * @return \Imagineer\Directory\Model\ResourceModel\Town|null
     */
    public function getItemById($townId)
    {
        foreach ($this->_items as $town) {
            if ($town->getTownId() == $townId) {
                return $town;
            }
        }
        return null;
    }
}
