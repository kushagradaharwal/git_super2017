<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Setup;

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
         * Update tables 'bakeway_counter' to add custom fields for carry forwarded quantity
         */
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
        	$setup->getConnection()->addColumn(
                $setup->getTable('bakeway_counter'),
                'carry_forwarded_qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'length' => 11,
                    'comment' => 'Carry Forwarded Quantity'
                ]
            );
        }
    }
}