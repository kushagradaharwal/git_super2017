<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PayoutsCalculation
 * @author    Bakeway
 */

namespace Bakeway\PayoutsCalculation\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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
        /**
         * Update tables 'marketplace_saleslist'
         */
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'tcs_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'TCS Calculated Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'convenience_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Convenience Fee Applied'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'convenience_fee_incl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Convenience Fee Applied Incl Tax'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'delivery_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Delivery Charge'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'delivery_fee_excl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Delivery Charge Incl Tax'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'commission_tax_percentage',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Commission Percentage'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'commission_incl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Commission Incl Tax'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'commission_tax_details',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Commission Tax Details'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'payment_gateway_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'PG Fee On Order'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'payment_gateway_percentage',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'PG Percentage On Order'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'payment_gateway_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'PG Fee On Order'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'payment_gateway_percentage',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'PG Percentage On Order'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'tcs_amount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'TCS Calculated Amount'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'convenience_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Convenience Fee Applied'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'convenience_fee_incl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Convenience Fee Applied Incl Tax'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'delivery_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Delivery Charge'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'delivery_fee_excl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'Delivery Charge Incl Tax'
                ]
            );

            /***
             * Dropping the colunmns from the marketplace_saleslist
             */
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'payment_gateway_fee'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'payment_gateway_percentage'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'tcs_amount'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'convenience_fee'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'convenience_fee_incl_tax'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'delivery_fee'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('marketplace_saleslist'),
                'delivery_fee_excl_tax'
            );
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_saleslist'),
                'actual_seller_amount_excl_tax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'default' => '0.00',
                    'comment' => 'PG Fee On Order',
                    'after' => 'actual_seller_amount'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_orders'),
                'tax_paid_by_bakeway',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Is Tax Paid By Bakeway'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            /** Adding columns to marketplace_transaction for inword file */
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_sellertransaction'),
                'transaction_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Transaction Status'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('marketplace_sellertransaction'),
                'transaction_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'length'  => 255,
                    'comment' => 'Transaction Filename'
                ]
            );

            /**
             * Creating table for bakeway payouts inword transaction
             */
            $table = $setup->getConnection()
                ->newTable($setup->getTable('bakeway_payouts_transaction'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'transaction_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true,'default' => null],
                    'Transaction Id'
                )
                ->addColumn(
                    'seller_transaction_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Seller Transaction Id'
                )
                ->addColumn(
                    'transaction_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    ['nullable' => true, 'default' => null],
                    'Bakeway Transaction Amount'
                )
                ->addColumn(
                    'transaction_file_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'Transaction Filename'
                )
                ->addColumn(
                    'transaction_status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => 0],
                    'Transaction Status'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Updated At'
                )
                ->setComment('Bakeway Payouts Transaction Table');
            $setup->getConnection()->createTable($table);
        }
    }
}