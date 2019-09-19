<?php

/**
 * Copyright © Imagineer. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Imagineer\Directory\Model\ResourceModel;

/**
 * County Resource Model
 *
 * @author Jose Luis Moya <jose@smartnow.tech>
 * 
 * @api
 * @since 1.0.0
 * 
 */
class County extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * Table with localized county names
     *
     * @var string
     */
    protected $_countyNameTable;

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
     * Define main and locale county name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_region_county', 'county_id');
        $this->_countyNameTable = $this->getTable('directory_region_county_name');
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

        $countyField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $connection->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            ['lrn' => $this->_countyNameTable],
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
     * Load object by county id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $countyId
     * @return $this
     */
    protected function _loadById($object, $countyId)
    {
        $connection = $this->getConnection();
        
        $select = $connection->select()->from(
            ['county' => $this->getMainTable()]
        )->where(
            'county.county_id = ?',
            $countyId
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Load object by county code
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $code
     * @return $this
     */
    protected function _loadByCode($object, $code)
    {
        $connection = $this->getConnection();
        
        $select = $connection->select()->from(
            ['county' => $this->getMainTable()]
        )->where(
            'county.code = ?',
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
     * Loads county by county id
     *
     * @param \Imagineer\Directory\Model\County $county
     * @param string $countyId
     *
     * @return $this
     */
    public function loadById(\Imagineer\Directory\Model\County $county, $countyId)
    {
        return $this->_loadById($county, $countyId);
    }

    /**
     * Loads county by county code
     *
     * @param \Imagineer\Directory\Model\County $county
     * @param string $code
     *
     * @return $this
     */
    public function loadByCode(\Imagineer\Directory\Model\County $county, $code)
    {
        return $this->_loadByCode($county, $code);
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
