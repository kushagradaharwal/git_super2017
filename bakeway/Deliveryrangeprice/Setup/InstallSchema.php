<?php
namespace Bakeway\Deliveryrangeprice\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
         *Start table  backway deliveryrangeprice table         
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bakeway_delivery_rangeprice')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            10,
            array('identity' => true, 'nullable' => false, 'primary' => true),
            'Id'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            10,
            array('nullable' => false),
            'Seller Id'
        )->addColumn(
            'field_id',
            Table::TYPE_INTEGER,
            10,
            array('nullable' => true),
            'Field Id'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            10,
            array('nullable' => false),
            'Active'
        )->addColumn(
            'from_kms',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'From Kms'
        )->addColumn(
            'to_kms',
            Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'To Kms'
        )
            ->addColumn(
                'delivery_price',
                Table::TYPE_DECIMAL,
                255,
                array('nullable' => false, 'default' => '0.0000'),
                'Delivery Price'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                255,
                array('nullable' => false, Table::TIMESTAMP_INIT),
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                255,
                array('nullable' => false, Table::TIMESTAMP_INIT_UPDATE),
                'Updated At'
            )
            ->addIndex(
                $installer->getIdxName('bakeway_delivery_rangeprice', ['from_kms']),
                ['from_kms']
            )
            ->addIndex(
                $installer->getIdxName('bakeway_delivery_rangeprice', ['to_kms']),
                ['to_kms']
            )
            ->addIndex(
                $installer->getIdxName('bakeway_delivery_rangeprice', ['delivery_price']),
                ['delivery_price']
            )->addIndex(
                $installer->getIdxName('bakeway_delivery_rangeprice', ['seller_id']),
                ['seller_id']
            );
        $installer->getConnection()->createTable($table);
        /*
         *End table  backway deliveryrangeprice table         
        */
        $installer->endSetup();
    }
}