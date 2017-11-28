<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Shopapi
 * @author    Bakeway
 */

namespace Bakeway\Shopapi\Setup;

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
         * Update tables 'vendor_shop' to add custom fields for logging
         */
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
        	$setup->getConnection()->addColumn(
                $setup->getTable('vendor_shop'),
                'open_close_logs',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Shop Open Close Logs'
                ]
            );
        }
    }
}