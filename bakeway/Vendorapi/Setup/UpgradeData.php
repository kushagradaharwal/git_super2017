<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface {
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            /*
             *  Get required tables
             */
            $statusTable = $installer->getTable('sales_order_status');
            $statusStateTable = $installer->getTable('sales_order_status_state');

            // Insert statuses
            $installer->getConnection()->insertArray(
                $statusTable,
                array(
                    'status',
                    'label'
                ),
                array(
                    array('status' => 'bakeway_partner_accepted', 'label' => 'Accepted By Partner'),
                    array('status' => 'bakeway_partner_rejected', 'label' => 'Rejected By Partner'),
                    array('status' => 'bakeway_order_ready', 'label' => 'Ready for delivery/pickup'),
                    array('status' => 'bakeway_order_out_for_delivery', 'label' => 'Out for delivery')
                )
            );

            // Insert states and mapping of statuses to states
            $installer->getConnection()->insertArray(
                $statusStateTable,
                array(
                    'status',
                    'state',
                    'is_default'
                ),
                array(
                    array(
                        'status' => 'bakeway_partner_accepted',
                        'state' => 'processing',
                        'is_default' => 0
                    ),
                    array(
                        'status' => 'bakeway_partner_rejected',
                        'state' => 'canceled',
                        'is_default' => 0
                    ),
                    array(
                        'status' => 'bakeway_order_ready',
                        'state' => 'processing',
                        'is_default' => 0
                    ),
                    array(
                        'status' => 'bakeway_order_out_for_delivery',
                        'state' => 'processing',
                        'is_default' => 0
                    )
                )
            );
        }
    }
}