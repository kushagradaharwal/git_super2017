<?php

namespace Bakeway\CommissionLog\Block\Adminhtml\Edit\Tab\Grid;

use Magento\Customer\Controller\RegistryConstants;

class Gridcommission extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Bakeway\Deliveryrangeprice\Model\ResourceModel\Rangeprice\Collection
     */
    protected $_collectionFactory;
    protected $_cmsPage;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Sales\Model\Resource\Order\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Bakeway\CommissionLog\Model\ResourceModel\CommissionLog\Collection $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Page $cmsPage,

        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        $this->_cmsPage = $cmsPage;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('commissionid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize(8)->setCurPage(1);
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }


    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        try {
            $collection = $this->_collectionFactory->load()
                ->setOrder("created_at", 'DESC')
                ->addFieldToFilter("seller_id", $this->getCustomerId());
            $this->setCollection($collection);
            parent::_prepareCollection();
            return $this;
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }


    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }


        return parent::_addColumnFilterToCollection($column);
    }


    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {


        $this->addColumn(
            'commission_rate',
            [
                'header' => __('Commission Rate'),
                'index' => 'commission_rate',
                'class' => 'commision',

            ]
        );
        $this->addColumn(
            'created_at',
            [
                'header' => __('Updated Date'),
                'index' => 'created_at',
                'type' => 'datetime',
            ]
        );
        $this->addColumn(
            'created_by',
            [
                'header' => __('Updated By'),
                'index' => 'created_by',
                'class' => 'setby'
            ]
        );


        return parent::_prepareColumns();
    }

    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('commissionlog/commission/index', ['_current' => true]);
    }


}