<?php

namespace Bakeway\Razorpay\Setup;

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

        /*
         *adding columns to table sales_order_payment
        */

        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_payment'),
                'razorpay_order_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay order id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_payment'),
                'razorpay_payment_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay payment id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_payment'),
                'razorpay_signature',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay Signature'
                ]
            );
            
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_payment_transaction'),
                'razorpay_order_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay order id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_payment_transaction'),
                'razorpay_payment_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay payment id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_payment_transaction'),
                'razorpay_signature',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay Signature'
                ]
            );
            
        }
        
         if (version_compare($context->getVersion(), '1.0.1', '<')) {
              $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'razorpay_order_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay order id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'razorpay_payment_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay payment id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'razorpay_signature',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Razorpay Signature'
                ]
            );
            
         }
    }
}