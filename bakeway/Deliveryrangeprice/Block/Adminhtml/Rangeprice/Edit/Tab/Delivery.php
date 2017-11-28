<?php
namespace Bakeway\Deliveryrangeprice\Block\Adminhtml\Rangeprice\Edit\Tab;
class Delivery extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmspage;
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Page $cmsPage,
        \Bakeway\Deliveryrangeprice\Helper\Data $helper,
        array $data = array()
    )
    {
        $this->_systemStore = $systemStore;
        $this->_cmsPage = $cmsPage;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('deliveryrangeprice_rangeprice');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Delivery Range')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

        //
        $_sellerselectFlag = "";
        if (!empty($model->getSellerId())):
            $_setEmail = $this->helper->getEmail($model->getSellerId());
            $fieldset->addField(
                'seller_id',
                'text',
                array(
                    'name' => 'seller_id',
                    'label' => __('Seller Email'),
                    'title' => __('Seller Email'),
                    'readonly' => true,
                    'class' => 'required-entry validate-email',
                )
            );
        else:
            $fieldset->addField(
                'seller_id',
                'text',
                array(
                    'name' => 'seller_id',
                    'label' => __('Seller Email'),
                    'title' => __('Seller Email'),
                    'class' => 'required-entry validate-email',
                )
            );
        endif;

        /*$fieldset->addField(
            'seller_id',
            'select',
            array(
                'name' => 'seller_id',
                'label' => __('Seller Email'),
                'title' => __('Seller Email'),
                'values' => $this->helper->getSellercollection(),
                'disabled' =>  $_sellerselectFlag,
                /*'required' => Delivery Range,
            )
        );
         */


        $fieldset->addField(
            'from_kms',
            'text',
            array(
                'name' => 'from_kms',
                'label' => __('From Kms'),
                'title' => __('From Kms'),
                'class' => 'required-entry validate-number',
            )
        );

        $fieldset->addField(
            'to_kms',
            'text',
            array(
                'name' => 'to_kms',
                'label' => __('To Kms'),
                'title' => __('To Kms'),
                'class' => 'required-entry validate-number',
            )
        );

        $fieldset->addField(
            'delivery_price',
            'text',
            array(
                'name' => 'delivery_price',
                'label' => __('Delivery Price'),
                'title' => __('Delivery Price'),
                'validate_class' => __('validate-number'),
                'class' => 'required-entry validate-number',
            )
        );


        $fieldset->addField(
            'is_active',
            'select',
            array(
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_cmsPage->getAvailableStatuses()
            )
        );
        /*{{CedAddFormField}}*/

        // if (!$model->getId()) {
        // $model->setData('status', $isElementDisabled ? '2' : '1');
        //}

        if ($model->getData('seller_id') != "") {
            $model->setData('seller_id', $_setEmail);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Delivery Range');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Delivery Range');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
