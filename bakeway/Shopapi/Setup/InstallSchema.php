<?php

/**
 * Bakeway Software.
 *
 * @category  Bakeway
 * @package   Bakeway_Shopapi
 * @author    Bakeway
 */

namespace Bakeway\Shopapi\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Create table 'vendor_shop'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('vendor_shop'))
                ->addColumn(
                        'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'ID'
                )
                ->addColumn(
                        'seller_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'default' => '0'], 'Seller ID'
                )
                ->addColumn(
                        'date', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 2, [], 'DATE'
                )
                ->addColumn(
                        'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'default' => '0'], 'Status'
                )
                ->addColumn(
                        'open_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Open Time'
                )
                ->addColumn(
                        'open_by', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'Open By'
                )
                ->addColumn(
                        'close_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Close Time'
                )
                ->addColumn(
                        'close_by', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'Close By'
                )
                ->setComment('Shopapi Open Close Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
