<?php
namespace Bakeway\Deliveryrangeprice\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
 
class Grid  extends  \Magento\Backend\Block\Widget\Grid\Extended
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
        \Bakeway\Deliveryrangeprice\Model\ResourceModel\Rangeprice\Collection $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Page $cmsPage,

        array $data = []
    ) {
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
        $this->setId('productGrid');
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
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
       try{
            
            
            $collection =$this->_collectionFactory->load()
                          ->addFieldToFilter('main_table.seller_id',$this->getCustomerId())
                ->addFieldToFilter('main_table.delivery_deleted', 0);
            $collection->getSelect()->joinLeft(
                ['cust' => $collection->getTable('customer_entity')],
                'main_table.seller_id = cust.entity_id',
                ['email']
            )->joinLeft(['userdata' => $collection->getTable('marketplace_userdata')],
                'main_table.seller_id = userdata.seller_id',
                ['shop_title']);
            
            $this->setCollection($collection);
            
            parent::_prepareCollection();
          
            return $this;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();die;
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
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

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
                'header' => __('Seller email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

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
                'filter_index'=>'main_table.is_active',
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
                        'base' => 'deliveryrangeprice/delivery/edit'
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
        return $this->getUrl('deliveryrangeprice/*/deliveryrangeprice', ['_current' => true,'collapse' => null]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'deliveryrangeprice/delivery/edit',
            ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }
}