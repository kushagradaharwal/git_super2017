<?php
namespace Bakeway\Deliveryrangeprice\Block\Adminhtml\Rangeprice;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    protected $_cmsPage;
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Bakeway\Deliveryrangeprice\Model\ResourceModel\Rangeprice\Collection $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Cms\Model\Page $cmsPage,
        \Bakeway\Deliveryrangeprice\Helper\Data $helper,
        array $data = []
    )
    {

        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_cmsPage = $cmsPage;
        $this->moduleManager = $moduleManager;
        $this->helper = $helper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

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
     * @return $this
     */
    protected function _prepareCollection()
    {
        try {


            $collection = $this->_collectionFactory->load()
                ->addFieldToFilter('main_table.delivery_deleted', 0);

            $collection->getSelect()->joinLeft(['cust' => $collection->getTable('customer_entity')],
                'main_table.seller_id = cust.entity_id',
                ['email']
            )->joinLeft(['userdata' => $collection->getTable('marketplace_userdata')],
                'main_table.seller_id = userdata.seller_id',
                ['shop_title']);

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
            'shop_title',
            [
                'header' => __('Shop Title'),
                'type' => 'text',
                'index' => 'shop_title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Seller Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        /*$this->addColumn(
            'seller_id',
            [
                'header' => __('Seller email'),
                'type' => 'options',
                'index' => 'seller_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'options' => $this->helper->getSellercollection(),
                'filter_index'=>'main_table.seller_id',
            ]
        );*/

        $this->addColumn(
            'from_kms',
            [
                'header' => __('From Kms'),
                'type' => 'number',
                'index' => 'from_kms',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'to_kms',
            [
                'header' => __('To Kms'),
                'type' => 'number',
                'index' => 'to_kms',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'delivery_price',
            [
                'header' => __('Delivery Price'),
                'type' => 'number',
                'index' => 'delivery_price',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'is_active',
                'options' => $this->_cmsPage->getAvailableStatuses(),
                'filter_index' => 'main_table.is_active',
            ]
        );


        $this->addColumn(
            'edit', [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'id',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );


        /*{{CedAddGridColumn}}*/

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('deliveryrangeprice/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('deliveryrangeprice/*/index', ['_current' => true]);
    }


    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'deliveryrangeprice/*/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}
