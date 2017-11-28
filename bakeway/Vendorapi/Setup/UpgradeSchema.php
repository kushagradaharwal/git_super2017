<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table "order_reject_reasons"
         */
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('bakeway_order_rejected_reasons')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'reason_content',
                Table::TYPE_TEXT,
                Null,
                ['nullable' => true, 'default' => Null],
                'Reason Content'
            )->addColumn(
                'is_for_seller',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true, 'default' => Null],
                'Is For Seller'
            )->addColumn(
                'is_for_customer',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true, 'default' => Null],
                'Is For Customer'
            )->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => true, 'default' => Null],
                'Is Active'
            )->setComment('Orders Reject Reasons');

            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}