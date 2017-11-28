<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Setup;

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
         * Create table 'vendor_counter'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('bakeway_counter'))
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
                        'sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'SKU'
                )
                ->addColumn(
                        'qty', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'default' => '0'], 'QTY'
                )
                ->addColumn(
                        'last_updated_by', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, null, [], 'Last Updated By'
                )
                ->addColumn(
                        'last_updated_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Close Time'
                )
                ->addColumn(
                        'offline_sale', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'default' => '0'], 'Offline Sale'
                )
                ->addColumn(
                        'online_sale', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'default' => '0'], 'Online Sale'
                )
                ->setComment('Vendor Counter Inventory Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
