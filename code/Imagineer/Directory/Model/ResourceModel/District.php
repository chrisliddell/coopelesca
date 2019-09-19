<?php

/*
 * Copyright © Imagineer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Imagineer\Directory\Model\ResourceModel;

/**
 * Description of District
 *
 * @author Jose Luis Moya <jose@smartnow.tech>
 * 
 * @api
 * @since 1.0.0
 */
class District extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Table with localized district names
     *
     * @var string
     */
    protected $_districtNameTable;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Define main and locale district name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_county_district', 'district_id');
        $this->_countyNameTable = $this->getTable('directory_county_district_name');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $connection = $this->getConnection();

        $locale = $this->_localeResolver->getLocale();
        $systemLocale = \Magento\Framework\AppInterface::DISTRO_LOCALE_CODE;

        $districtField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $connection->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            ['lrn' => $this->_districtNameTable],
            "{$countyField} = lrn.county_id AND {$condition}",
            []
        );

        if ($locale != $systemLocale) {
            $nameExpr = $connection->getCheckSql('lrn.county_id is null', 'srn.name', 'lrn.name');
            $condition = $connection->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                ['srn' => $this->_countyNameTable],
                "{$countyField} = srn.county_id AND {$condition}",
                ['name' => $nameExpr]
            );
        } else {
            $select->columns(['name'], 'lrn');
        }

        return $select;
    }

    /**
     * Load object by region id and code or default name
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $regionId
     * @param string $value
     * @param string $field
     * @return $this
     */
    protected function _loadByRegion($object, $regionId, $value, $field)
    {
        $connection = $this->getConnection();
        $locale = $this->_localeResolver->getLocale();
        $joinCondition = $connection->quoteInto('rname.county_id = county.county_id AND rname.locale = ?', $locale);
        $select = $connection->select()->from(
            ['county' => $this->getMainTable()]
        )->joinLeft(
            ['rname' => $this->_countyNameTable],
            $joinCondition,
            ['name']
        )->where(
            'county.region_id = ?',
            $regionId
        )->where(
            "county.{$field} = ?",
            $value
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Load object by district id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $districtId
     * @return $this
     */
    protected function _loadById($object, $districtId)
    {
        $connection = $this->getConnection();
        
        $select = $connection->select()->from(
            ['district' => $this->getMainTable()]
        )->where(
            'district.district_id = ?',
            $districtId
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Load object by district code
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $code
     * @return $this
     */
    protected function _loadByCode($object, $code)
    {
        $connection = $this->getConnection();
        
        $select = $connection->select()->from(
            ['district' => $this->getMainTable()]
        )->where(
            'district.code = ?',
            $code
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Loads county by county code and region id
     *
     * @param \Imagineer\Directory\Model\County $county
     * @param string $countyCode
     * @param string $regionId
     *
     * @return $this
     */
    // public function loadByCode(\Imagineer\Directory\Model\County $county, $countyCode, $regionId)
    // {
    //     return $this->_loadByRegion($county, $regionId, (string)$countyCode, 'code');
    // }

    /**
     * Loads district by district id
     *
     * @param \Imagineer\Directory\Model\District $district
     * @param string $districtId
     *
     * @return $this
     */
    public function loadById(\Imagineer\Directory\Model\District $district, $districtId)
    {
        return $this->_loadById($district, $districtId);
    }

    /**
     * Loads district by district code
     *
     * @param \Imagineer\Directory\Model\District $district
     * @param string $code
     *
     * @return $this
     */
    public function loadByCode(\Imagineer\Directory\Model\District $district, $code)
    {
        return $this->_loadByCode($district, $code);
    }

    /**
     * Load data by region id and default county name
     *
     * @param \Imagineer\Directory\Model\County $county
     * @param string $countyName
     * @param string $regionId
     * @return $this
     */
    public function loadByName(\Imagineer\Directory\Model\County $county, $countyName, $regionId)
    {
        return $this->_loadByRegion($county, $regionId, (string)$countyName, 'default_name');
    }
}
