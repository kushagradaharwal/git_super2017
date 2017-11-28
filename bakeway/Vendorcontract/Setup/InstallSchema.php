<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcontract
 * @author    Bakeway
 */

namespace Bakeway\Vendorcontract\Setup;

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
        $table = $installer->getConnection()
            ->newTable($installer->getTable('bakeway_vendor_contracts'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'seller_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => '0'],
                'Seller Id'
            )
            ->addColumn(
                'start_date',
                Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Brand Status'
            )
            ->addColumn(
                'end_date',
                Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Brand Status'
            )
            ->addColumn(
                'bakeway_poc',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => null],
                'Bakeway Point Of Contact'
            )
            ->setComment('Bakeway Contracts');
        $installer->getConnection()->createTable($table);
        $installer->getConnection()->addForeignKey(
            $installer->getFkName(
                'bakeway_vendor_contracts',
                'seller_id',
                'marketplace_userdata',
                'seller_id'
            ),
            'bakeway_vendor_contracts',
            'seller_id',
            'marketplace_userdata',
            'seller_id'
        );
        $installer->endSetup();

    }
}