<?php
namespace Bakeway\CommissionLog\Setup;

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
         *Start table  bakeway CommisonLog table
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bakeway_commissionlog')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            array('identity' => true, 'nullable' => false, 'primary' => true,'unsigned' => true),
            'Id'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            array('nullable' => false,'unsigned' => true),
            'Seller Id'
        )->addColumn(
            'commission_rate',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => true, 'nullable' => false, 'default' => '0.0000'],
            'Commission Rate'
        )->addColumn(
        'created_at',
        Table::TYPE_TIMESTAMP,
        255,
        array('nullable' => false, Table::TIMESTAMP_INIT),
        'Created At'
    )
        ->addColumn(
            'created_by',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Created By'
        );


          $installer->getConnection()->createTable($table);
        /*
         *End table  backway commisonlog table
        */
        $installer->endSetup();
    }
}