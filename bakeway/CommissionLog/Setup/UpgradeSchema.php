<?php

namespace Bakeway\CommissionLog\Setup;

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
         *adding columns deleted and seller log
        */

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $setup->getConnection()->addForeignKey(
                $setup->getFkName(
                    'bakeway_commissionlog',
                    'seller_id',
                    'customer_entity',
                    'entity_id'
                ),
                $setup->getTable('bakeway_commissionlog'),
                'seller_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );


        }
    }
}