<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomerWebapi
 * @author    Bakeway
 */

namespace Bakeway\CustomerWebapi\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config as EavConfig;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface {

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * UpgradeSchema constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     * @param EavConfig $eavConfig
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        EavConfig $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'lastname',
                'is_required',
                false
            );
            $eavSetup->updateAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'firstname',
                'is_required',
                false
            );
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'social_profile_pic',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'label' => 'Social Profile Pic',
                    'input' => 'text',
                    'required' => false,
                    'nullable' => true,
                    'default' => null,
                    'sort_order' => '200',
                    'position' => '200',
                    'system' => false
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'social_profile_pic_platform',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'label' => 'Social Profile Pic Platform',
                    'input' => 'text',
                    'required' => false,
                    'nullable' => true,
                    'default' => null,
                    'sort_order' => '201',
                    'position' => '201',
                    'system' => false
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'fb_user_id',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'label' => 'Facebook User Id',
                    'input' => 'text',
                    'required' => false,
                    'nullable' => true,
                    'default' => null,
                    'sort_order' => '202',
                    'position' => '202',
                    'system' => false
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'gplus_user_id',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'label' => 'Google Plus User Id',
                    'input' => 'text',
                    'required' => false,
                    'nullable' => true,
                    'default' => null,
                    'sort_order' => '203',
                    'position' => '203',
                    'system' => false
                ]
            );
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'is_password_editable',
                [
                    'type' => 'int',
                    'label' => 'Password Editable',
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'required' => false,
                    'default' => '1',
                    'sort_order' => '204',
                    'position' => '204',
                    'system' => false
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'social_profile_pic');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'social_profile_pic_platform');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'fb_user_id');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'gplus_user_id');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'is_password_editable');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                \Magento\Customer\Model\Customer::ENTITY,
                'mobile_number',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'label' => 'Mobile Number',
                    'input' => 'text',
                    'required' => false,
                    'nullable' => true,
                    'default' => null,
                    'sort_order' => '205',
                    'position' => '205',
                    'system' => false
                ]
            );
            $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'mobile_number');
            $attribute->setData(
                'used_in_forms',
                ['adminhtml_customer', 'customer_account_create', 'customer_account_edit']
            );
            $attribute->addData([
                'attribute_set_id' => 1,
                'attribute_group_id' => 1
            ]);
            $attribute->save();
        }
    }
}