<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Customer account form block.
 */
class StoreCloneTab extends Generic implements TabInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_isSeller;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_isSeller = 0;
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
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
        return __('Clone a store');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Clone a store');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        $coll = $this->_objectManager
            ->create('Webkul\Marketplace\Block\Adminhtml\Customer\Edit')
            ->getMarketplaceUserCollection();
        $isSeller = false;
        foreach ($coll as $row) {
            $isSeller = $row->getIsSeller();
        }
        $this->_isSeller = $isSeller;
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
        $coll = $this->_objectManager
            ->create('Webkul\Marketplace\Block\Adminhtml\Customer\Edit')
            ->getMarketplaceUserCollection();
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
        $form->setHtmlIdPrefix('storeclone_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Search a store by store name')]
        );

        $fieldset->addField(
            'storename',
            'text',
            [
                'name' => 'storename',
                'data-form-part' => $this->getData('target_form'),
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'value' => '',
            ]
        );
        $fieldset->addField(
            'store_search',
            'button',
            [
                'name' => 'store_search',
                'data-form-part' => $this->getData('target_form'),
                'value' => __('Search Store'),
                'title' => __('Search Store'),
            ]
        );
        $this->setForm($form);

        return $this;
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
            'Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\SearchStoreJs'
        )->toHtml();

        return $html;
    }
}
