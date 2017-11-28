<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorPaymentInfo
 * @author    Bakeway
 */

namespace Bakeway\VendorPaymentInfo\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Customer Seller form block.
 */
class Tabs extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_dob = null;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_country;
    /**
     * @var Webkul\Marketplace\Helper\Data
     */
    protected $_marketplaceHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\ResourceModel\Country\Collection $country,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\Marketplace\Helper\Data $marketplaceHelper,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_systemStore = $systemStore;
        $this->_objectManager = $objectManager;
        $this->_country = $country;
        $this->_marketplaceHelper = $marketplaceHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(
            RegistryConstants::CURRENT_CUSTOMER_ID
        );
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Seller Payment and Tax Information');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Seller Payment and Tax Information');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        $coll = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getMarketplaceUserCollection();
        $isSeller = false;
        foreach ($coll as $row) {
            $isSeller = $row->getIsSeller();
        }
        if ($this->getCustomerId() && $isSeller) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        $coll = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getMarketplaceUserCollection();
        $isSeller = false;
        foreach ($coll as $row) {
            $isSeller = $row->getIsSeller();
        }
        if ($this->getCustomerId() && $isSeller) {
            return false;
        }

        return true;
    }

    /**
     * Tab class getter.
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content.
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('marketplace_');
        $customerId = $this->_coreRegistry->registry(
            RegistryConstants::CURRENT_CUSTOMER_ID
        );
        $storeid = $this->_storeManager->getStore()->getId();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Seller Payment And Tax Related Information')]
        );
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);
        $partner = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getSellerInfoCollection();

        $fieldset->addField(
            'userdata_bank_name',
            'text',
            [
                'name' => 'userdata_bank_name',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Bank Name'),
                'title' => __('Bank Name'),
                'value' => $partner['userdata_bank_name'],
            ]
        );
        $fieldset->addField(
            'store_owner_bank_registered_name',
            'text',
            [
                'name' => 'store_owner_bank_registered_name',
                'class' => 'validate-alphanum-with-spaces',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Benificiary Name '),
                'title' => __('Benificiary Name '),
                'value' => $partner['store_owner_bank_registered_name'],
            ]
        );

        $fieldset->addField(
            'store_owner_ bank_ifsc',
            'text',
            [
                'name' => 'store_owner_bank_ifsc',
                'class' => 'validate-alphanum',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('IFSC Code'),
                'title' => __('IFSC Code'),
                'value' => $partner['store_owner_bank_ifsc'],
            ]
        );
        $fieldset->addField(
            'store_owner_bank_account_number',
            'text',
            [
                'name' => 'store_owner_bank_account_number',
                'class' => 'validate-number',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Account Number'),
                'title' => __('Account Number'),
                'value' => $partner['store_owner_bank_account_number'],
            ]
        );
        $fieldset->addField(
            'store_owner_bank_account_type',
            'text',
            [
                'name' => 'store_owner_bank_account_type',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Account Type'),
                'title' => __('Account Type'),
                'value' => $partner['store_owner_bank_account_type'],
            ]
        );

        /* $fieldset->addField(
             'store_owner_bank_micr',
             'text',
             [
                 'name' => 'store_owner_bank_micr',
                 'class' => '',
                 'data-form-part' => $this->getData('target_form'),
                 'label' => __('Store Owner Bank MICR Code'),
                 'title' => __('Store Owner Bank MICR Code'),
                 'value' => $partner['store_owner_bank_micr'],
             ]
         );*/
        $fieldset->addField(
            'userdata_gstin_number',
            'text',
            [
                'name' => 'userdata_gstin_number',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('GSTIN Number'),
                'title' => __('GSTIN Number'),
                'value' => $partner['userdata_gstin_number'],
            ]
        );

        $fieldset->addField(
            'userdata_tin_number',
            'text',
            [
                'name' => 'userdata_tin_number',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('TIN Number'),
                'title' => __('TIN Number'),
                'value' => $partner['userdata_tin_number'],
            ]
        );
        $fieldset->addField(
            'userdata_tan_number',
            'text',
            [
                'name' => 'userdata_tan_number',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('TAN Number'),
                'title' => __('TAN Number'),
                'value' => $partner['userdata_tan_number'],
            ]
        );
        $fieldset->addField(
            'userdata_pan_number',
            'text',
            [
                'name' => 'userdata_pan_number',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('PAN Number'),
                'title' => __('PAN Number'),
                'value' => $partner['userdata_pan_number'],
            ]
        );
        $fieldset->addField(
            'userdata_cgst',
            'text',
            [
                'name' => 'userdata_cgst',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('CGST'),
                'title' => __('CGST'),
                'value' => $partner['userdata_cgst'],
            ]
        );
        $fieldset->addField(
            'userdata_sgst',
            'text',
            [
                'name' => 'userdata_sgst',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('SGST'),
                'title' => __('SGST'),
                'value' => $partner['userdata_sgst'],
            ]
        );
        $fieldset->addField(
            'userdata_igst',
            'text',
            [
                'name' => 'userdata_igst',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('IGST'),
                'title' => __('IGST'),
                'value' => $partner['userdata_igst'],
            ]
        );
        $fieldset->addField(
            'userdata_cancelled_cheque',
            'file',
            [
                'name' => 'userdata_cancelled_cheque',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Cancelled Cheque Photo Upload'),
                'title' => __('Cancelled Cheque Photo Upload'),
                'value' => $partner['userdata_cancelled_cheque'],
                'after_element_html' => '<label style="width:100%;">
                    Allowed File Type : [jpg, jpeg, gif, png]
                </label>
                <img style="margin:5px 0;width:700px;" 
                src="'.$this->getBaseUrl().'pub/media/avatar/'.$partner['userdata_cancelled_cheque'].'"
                />',
            ]
        );
        $fieldset->addField(
            'userdata_agreement_document',
            'file',
            [
                'name' => 'userdata_agreement_document',
                'class' => '',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Agreement Document Upload'),
                'title' => __('Agreement Document Upload'),
                'value' => $partner['userdata_agreement_document'],
                'after_element_html' => '<label style="width:100%;">
                    Allowed File Type : [pdf,xls,xlsx,xml,csv,doc,docx]
                </label>
                <a target="_blank" href="'.$this->getBaseUrl().'pub/media/avatar/'.$partner['userdata_agreement_document'].'"
                >'.$partner['userdata_agreement_document'].'</a>',
            ]
        );



        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->initForm();

            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Js'
        )->toHtml();

        return $html;
    }
}
