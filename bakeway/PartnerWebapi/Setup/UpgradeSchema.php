<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\PartnerWebapi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\DB\Ddl\TriggerFactory
     */
    protected $triggerFactory;

    public function __construct(
        \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
    )
    {
        $this->triggerFactory = $triggerFactory;
    }


    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Update tables 'marketplace_userdata' add store_geo_point
         *
         */
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $installer = $setup;

            $installer->startSetup();

            /**
             * SQL to change the lat, long data types
             * SQL to add the spatial point data type and spatial index
             */
            $installer->run("
                ALTER TABLE  `marketplace_userdata` 
                  CHANGE  `store_latitude`  `store_latitude` FLOAT( 10, 6 ) NULL DEFAULT NULL COMMENT  'Store Latitude',
                  CHANGE  `store_longitude`  `store_longitude` FLOAT( 10, 7 ) NULL DEFAULT NULL COMMENT  'Store Longitude',
                  ADD  `store_geo_point` POINT NOT NULL COMMENT  'Store Geo Location Point' AFTER  `store_longitude`,
                   ADD SPATIAL INDEX `store_geo_point` (`store_geo_point`);
            ");

            /**
             * Insert Before Trigger to add the values for the store_geo_point
             */
            $triggerName = 'marketplace_userdata_insert_before';

            /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_BEFORE)
                ->setEvent(\Magento\Framework\DB\Ddl\Trigger::EVENT_INSERT)
                ->setTable($setup->getTable('marketplace_userdata'));

            $trigger->addStatement($this->buildStatement());

            $setup->getConnection()->dropTrigger($trigger->getName());
            $setup->getConnection()->createTrigger($trigger);

            /**
             * Update Before Trigger to update the values for the store_geo_point
             */
            $triggerName = 'marketplace_userdata_update_before';

            /** @var \Magento\Framework\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(\Magento\Framework\DB\Ddl\Trigger::TIME_BEFORE)
                ->setEvent(\Magento\Framework\DB\Ddl\Trigger::EVENT_UPDATE)
                ->setTable($setup->getTable('marketplace_userdata'));

            $trigger->addStatement($this->buildStatement());

            $setup->getConnection()->dropTrigger($trigger->getName());
            $setup->getConnection()->createTrigger($trigger);

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer = $setup;

            $installer->startSetup();

            /**
             * Adding the average rating column to the marketplace_userdata
             */
            $installer->getConnection()->addColumn(
                $installer->getTable('marketplace_userdata'),
                'average_ratings',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => '00.00',
                    'length' => '2,2',
                    'comment' => 'Store Average Ratings'
                ]
            );
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $installer = $setup;

            $installer->startSetup();

            /**
             * Adding Database City Table
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('bakeway_cities'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'City Name'
                )
                ->addColumn(
                    'short_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => null],
                    'City Short Code'
                )
                ->addColumn(
                    'is_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                    'Is Active'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Creation Time'
                )
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Update Time'
                )
                ->setComment('Bakeway Cities Table');
            $installer->getConnection()->createTable($table);

            /**
             * Alter city column for the marketplace_userdata
             */
            $installer->getConnection()->modifyColumn(
                $installer->getTable('marketplace_userdata'),
                'store_city',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => '0'
                ]
            );

            $installer->getConnection()->addForeignKey(
                $installer->getFkName(
                    'marketplace_userdata',
                    'store_city',
                    'bakeway_cities',
                    'id'
                ),
                'marketplace_userdata',
                'store_city',
                'bakeway_cities',
                'id'
            );

            $installer->endSetup();
        }
    }

    /**
     * @return string
     */
    public function buildStatement()
    {
        $triggersql = "
                    IF (NEW.store_longitude IS NULL) THEN
                        SET NEW.store_longitude = 00.000000;
                    END IF;
                    IF (NEW.store_latitude IS NULL) THEN
                        SET NEW.store_latitude = 00.000000;
                    END IF;
                    SET NEW.store_geo_point = Point(NEW.store_latitude, NEW.store_longitude);";
        return $triggersql;
    }
}