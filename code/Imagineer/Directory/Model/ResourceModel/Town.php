<?php

/*
 * Copyright © Imagineer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Imagineer\Directory\Model\ResourceModel;

/**
 * Description of Town
 *
 * @author Jose Luis Moya <jose@smartnow.tech>
 *
 * @api
 * @since 1.0.0
 */
class Town extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Table with localized town names
     *
     * @var string
     */
    protected $_townNameTable;

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
        $this->_init('directory_district_town', 'town_id');
        $this->_districtNameTable = $this->getTable('directory_district_town_name');
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

        $townField = $connection->quoteIdentifier($this->getMainTable() . '.' . $this->getIdFieldName());

        $condition = $connection->quoteInto('lrn.locale = ?', $locale);
        $select->joinLeft(
            ['lrn' => $this->_townNameTable],
            "{$townField} = lrn.town_id AND {$condition}",
            []
        );

        if ($locale != $systemLocale) {
            $nameExpr = $connection->getCheckSql('lrn.town_id is null', 'srn.name', 'lrn.name');
            $condition = $connection->quoteInto('srn.locale = ?', $systemLocale);
            $select->joinLeft(
                ['srn' => $this->_townNameTable],
                "{$townField} = srn.town_id AND {$condition}",
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
    protected function _loadByDistrict($object, $regionId, $value, $field)
    {
        $connection = $this->getConnection();
        $locale = $this->_localeResolver->getLocale();
        $joinCondition = $connection->quoteInto('rname.town_id = town.town_id AND rname.locale = ?', $locale);
        $select = $connection->select()->from(
            ['town' => $this->getMainTable()]
        )->joinLeft(
            ['rname' => $this->_townNameTable],
            $joinCondition,
            ['name']
        )->where(
            'town.county_id = ?',
            $regionId
        )->where(
            "town.{$field} = ?",
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
     * Load object by town id
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $townId
     * @return $this
     */
    protected function _loadById($object, $townId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            ['town' => $this->getMainTable()]
            )->where(
                'town.town_id = ?',
                $townId
                );

            $data = $connection->fetchRow($select);
            if ($data) {
                $object->setData($data);
            }

            $this->_afterLoad($object);

            return $this;
    }

    /**
     * Load object by town code
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param int $code
     * @return $this
     */
    protected function _loadByCode($object, $code)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            ['town' => $this->getMainTable()]
            )->where(
                'town.code = ?',
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
     * @param \Imagineer\Directory\Model\County $district
     * @param string $districtCode
     * @param string $regionId
     *
     * @return $this
     */
    // public function loadByCode(\Imagineer\Directory\Model\District $district, $districtCode, $regionId)
    // {
    //     return $this->_loadByRegion($district, $regionId, (string)$districtCode, 'code');
    // }

    /**
     * Loads towns by town id
     *
     * @param \Imagineer\Directory\Model\Town $town
     * @param string $townId
     *
     * @return $this
     */
    public function loadById(\Imagineer\Directory\Model\Town $town, $townId)
    {
        return $this->_loadById($town, $townId);
    }

    /**
     * Loads towns by town code
     *
     * @param \Imagineer\Directory\Model\Town $town
     * @param string $code
     *
     * @return $this
     */
    public function loadByCode(\Imagineer\Directory\Model\Town $town, $code)
    {
        return $this->_loadByCode($town, $code);
    }

    /**
     * Load data by region id and default county name
     *
     * @param \Imagineer\Directory\Model\County $town
     * @param string $townName
     * @param string $districtId
     * @return $this
     */
    public function loadByName(\Imagineer\Directory\Model\Town $town, $townName, $districtId)
    {
        return $this->_loadByRegion($town, $districtId, (string)$townName, 'default_name');
    }
}