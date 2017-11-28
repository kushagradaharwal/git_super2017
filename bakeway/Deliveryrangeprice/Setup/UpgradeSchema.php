<?php

namespace Bakeway\Deliveryrangeprice\Setup;

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

        /*
         *adding columns deleted and seller log
        */

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable('bakeway_delivery_rangeprice'),
                'delivery_deleted',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length'    => 10,
                    'comment'   => 'Delivery deleted flag'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('bakeway_delivery_rangeprice'),
                'seller_log',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'comment'   => 'Deleting log details'
                ]
            );

            
        }
    }
}