<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Imagineer\Directory\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 *
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     *
     * {@inheritdoc} @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'directory_region_county'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_region_county'))
            ->addColumn('county_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ], 'County Id')
            ->addColumn('region_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'nullable' => false,
            'unsigned' => true
        ], 'Region Id')
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, [
            'nullable' => true,
            'default' => null
        ], 'County code')
            ->addColumn('default_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'County Name')
            ->addIndex($installer->getIdxName('directory_region_county', [
            'region_id'
        ]), [
            'region_id'
        ])
            ->setComment('Directory Region County');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'directory_region_county_name'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_region_county_name'))
            ->addColumn('locale', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 8, [
            'nullable' => false,
            'primary' => true,
            'default' => false
        ], 'Locale')
            ->addColumn('county_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'default' => '0'
        ], 'County Id')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
            'nullable' => true,
            'default' => null
        ], 'County Name')
            ->addIndex($installer->getIdxName('directory_region_county_name', [
            'county_id'
        ]), [
            'county_id'
        ])
            ->addForeignKey($installer->getFkName('directory_region_county_name', 'county_id', 'directory_region_county', 'county_id'), 'county_id', $installer->getTable('directory_region_county'), 'county_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Directory Region County Name');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'directory_county_district'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_county_district'))
            ->addColumn('district_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ], 'District Id')
            ->addColumn('county_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'nullable' => false,
            'unsigned' => true
        ], 'County Id')
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, [
            'nullable' => true,
            'default' => null
        ], 'District code')
            ->addColumn('default_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'District Name')
            ->addIndex($installer->getIdxName('directory_county_district', [
            'district_id'
        ]), [
            'district_id'
        ])
            ->setComment('Directory County District');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'directory_county_district_name'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_county_district_name'))
            ->addColumn('locale', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 8, [
            'nullable' => false,
            'primary' => true,
            'default' => false
        ], 'Locale')
            ->addColumn('district_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'default' => '0'
        ], 'District Id')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
            'nullable' => true,
            'default' => null
        ], 'District Name')
            ->addIndex($installer->getIdxName('directory_county_district_name', [
            'district_id'
        ]), [
            'district_id'
        ])
            ->addForeignKey($installer->getFkName('directory_county_district_name', 'district_id', 'directory_county_district', 'district_id'), 'district_id', $installer->getTable('directory_county_district'), 'district_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Directory County District Name');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'directory_district_town'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_district_town'))
            ->addColumn('town_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ], 'Town Id')
            ->addColumn('district_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'nullable' => false,
            'unsigned' => true
        ], 'District Id')
            ->addColumn('code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, [
            'nullable' => true,
            'default' => null
        ], 'Town code')
            ->addColumn('default_name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Town Name')
            ->addIndex($installer->getIdxName('directory_district_town', [
            'town_id'
        ]), [
            'town_id'
        ])
            ->setComment('Directory District Town');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'directory_district_town_name'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('directory_district_town_name'))
            ->addColumn('locale', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 8, [
            'nullable' => false,
            'primary' => true,
            'default' => false
        ], 'Locale')
            ->addColumn('town_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'default' => '0'
        ], 'Town Id')
            ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
            'nullable' => true,
            'default' => null
        ], 'District Name')
            ->addIndex($installer->getIdxName('directory_district_town_name', [
            'town_id'
        ]), [
            'town_id'
        ])
            ->addForeignKey($installer->getFkName('directory_district_town_name', 'town_id', 'directory_district_town', 'town_id'), 'town_id', $installer->getTable('directory_district_town'), 'town_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
            ->setComment('Directory District Town Name');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
