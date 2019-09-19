<?php

namespace Imagineer\AddressSetup\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

         /**
         * Poblado SECTION
         */
        $customerSetup->addAttribute('customer_address', 'town', [
            'label' => 'Poblado',
            'input' => 'select',
            'type' => 'varchar',
            'source' => 'Imagineer\AddressSetup\Model\Eav\Entity\Attribute\Source\Town',
            'required' => true,
            'position' => 103,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'town')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address'
            ]]);
        $attribute->save();

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'town',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Poblado'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'town',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Poblado'
            ]
        );
                
        /**
         * Distrito SECTION
         */
        $customerSetup->addAttribute('customer_address', 'district', [
            'label' => 'Distrito',
            'input' => 'select',
            'type' => 'varchar',
            'source' => 'Imagineer\AddressSetup\Model\Eav\Entity\Attribute\Source\District',
            'required' => true,
            'position' => 102,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);


        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'district')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address'
            ]]);
        $attribute->save();

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'district',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Distrito'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'district',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Distrito'
            ]
        );
        
        
        /**
         * Canton SECTION
         */
        $customerSetup->addAttribute('customer_address', 'county', [
            'label' => 'Cantón',
            'input' => 'select',
            'type' => 'varchar',
            'source' => 'Imagineer\AddressSetup\Model\Eav\Entity\Attribute\Source\County',
            'required' => true,
            'position' => 101,
            'visible' => true,
            'system' => false,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);


        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'county')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address'
            ]]);
        $attribute->save();

        $setup->getConnection()->addColumn(
            $setup->getTable('quote_address'),
            'county',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Canton'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_address'),
            'county',
            [
                'type' => 'text',
                'length' => 255,
                'comment' => 'Cantón'
            ]
        );                
    }
}
