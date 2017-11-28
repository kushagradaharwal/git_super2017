<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
    	/**
         * Update tables 'quote','sales_order','sales_order_item' to add custom fields for logging
         */
        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $quote = 'quote';
            $quoteItem = 'quote_item';
            $orderTable = 'sales_order';
            $orderItemTable = 'sales_order_item';
            $invoiceTable = 'sales_invoice';
            $invoiceItemTable = 'sales_invoice_item';

            /**
             * quote table attributes 'delivery_type', 'delivery_time'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($quote),
                'delivery_type',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Delivery Type Pickup/Delivery'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable($quote),
                'delivery_time',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'comment'   => 'Delivery Time'
                ]
            );

            /**
             * sales_order table attributes 'delivery_type', 'delivery_time'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'delivery_type',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Delivery Type Pickup/Delivery'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'delivery_time',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'comment'   => 'Delivery Time'
                ]
            );

            /**
             * sales_invoice table attributes 'delivery_type', 'delivery_time'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceTable),
                'delivery_type',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Delivery Type Pickup/Delivery'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceTable),
                'delivery_time',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'comment'   => 'Delivery Time'
                ]
            );

            /**
             * quote_item table attribute 'custom_message'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($quoteItem),
                'custom_message',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Item Custom Message'
                ]
            );

            /**
             * sales_order_item table attribute 'custom_message'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderItemTable),
                'custom_message',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Item Custom Message'
                ]
            );

            /**
             * sales_invoice_item table attribute 'custom_message'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceItemTable),
                'custom_message',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Item Custom Message'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $quote = 'quote';
            $orderTable = 'sales_order';
            $invoiceTable = 'sales_invoice';

            /**
             * quote table attributes 'customer_notes'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($quote),
                'customer_notes',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Bakeway Customer Notes'
                ]
            );

            /**
             * sales_order table attributes 'customer_notes'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'customer_notes',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Bakeway Customer Notes'
                ]
            );

            /**
             * sales_invoice table attributes 'customer_notes'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceTable),
                'customer_notes',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment'   => 'Bakeway Customer Notes'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $orderTable = 'sales_order';
            $invoiceTable = 'sales_invoice';

            /**
             * sales_order table attributes 'order_tracking_token'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'order_tracking_token',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Order Tracking Token'
                ]
            );

            /**
             * sales_invoice table attributes 'order_tracking_token'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceTable),
                'order_tracking_token',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Order Tracking Token'
                ]
            );
        }
        
        
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $orderItemTable = 'sales_order_item';

          /*
           * column item_image_url
           */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderItemTable),
                'item_image_url',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Item Image Url'
                ]
            );
             /*
           * column item_flavour
           */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderItemTable),
                'item_flavour',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Item Flavour'
                ]
            );
             /*
           * column item_weight
           */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderItemTable),
                'item_weight',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Item Weight'
                ]
            );
             /*
           * column item_ingredient
           */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderItemTable),
                'item_ingredient',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Item Ingredient'
                ]
            );
 
        }

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $quote = 'quote';
            $orderTable = 'sales_order';
            $invoiceTable = 'sales_invoice';

            /**
             * quote table attributes 'store_unique_name'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($quote),
                'customer_notes',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Store Unique Name'
                ]
            );

            /**
             * sales_order table attributes 'store_unique_name'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($orderTable),
                'store_unique_name',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Store Unique Name'
                ]
            );

            /**
             * sales_invoice table attributes 'store_unique_name'
             */
            $setup->getConnection()->addColumn(
                $setup->getTable($invoiceTable),
                'store_unique_name',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Store Unique Name'
                ]
            );
        }
        
    }
}