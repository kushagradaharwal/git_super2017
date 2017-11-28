<?php
namespace Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid;

use \Magento\Customer\Controller\RegistryConstants;

class Sellers extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Webkul\Marketplace\Model\SellerFactory
     */
    protected $_sellerFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Webkul\Marketplace\Model\SellerFactory $sellerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\Marketplace\Model\SellerFactory $sellerFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_sellerFactory = $sellerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('seller_search_result_grid');
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $querystring = $this->getRequest()->getParam('storename');
        $collection = $this->_sellerFactory->create()->getCollection()
            ->addFieldToFilter(['shop_url', 'shop_title'],
                [
                    [["like" => '%' . $querystring . '%']],
                    [["like" => '%' . $querystring . '%']]
                ]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'sortable' => true
            ]
        );
        $this->addColumn(
            'shop_url',
            [
                'header' => __('Shop Url'),
                'index' => 'shop_url'
            ]
        );
        $this->addColumn(
            'shop_title',
            [
                'header' => __('Shop Title'),
                'index' => 'shop_title'
            ]
        );
        $this->addColumn(
            'seller_id',
            [
                'header' => __('Action'),
                'sortable' => true,
                'index' => 'seller_id',
                'renderer'  => 'Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Renderer\SellerAction'

            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        $querystring = $this->getRequest()->getParam('storename');
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('storeclone/seller/searchstore', ['_current' => true,'storename'=>$querystring,'id'=>$id]);
    }
}
