<?php
namespace Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid;

use \Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Webkul\Marketplace\Model\Product as VendorProduct;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var ProductCollectionFactory
     */
    protected $productCollection;

    /**
     *@var VendorProduct
     */
    protected $vendorProduct;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ProductCollectionFactory $productCollection
     * @param VendorProduct $vendorProduct
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        ProductCollectionFactory $productCollection,
        VendorProduct $vendorProduct,
        array $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->productCollection = $productCollection;
        $this->vendorProduct = $vendorProduct;
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
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $vendorId = $this->getRequest()->getParam('sellerId');
        $storeCollection = $this->vendorProduct->getCollection()
            ->addFieldToFilter(
                'seller_id',
                $vendorId
            )->addFieldToSelect(
                ['mageproduct_id']
            );
        $storeCollection->getSelect()->group('mageproduct_id');

        $storeProductIDs = $storeCollection->getAllIds();

        $collection = $this->productCollection->create()
            ->addFieldToFilter(
                'entity_id',
                ['in'=>$storeProductIDs]
            )->addFieldToSelect('*');
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
            'name',
            [
                'header' => __('Produc Name'),
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Product SKU'),
                'index' => 'sku'
            ]
        );
        $this->addColumn(
            'vendor_product_code',
            [
                'header' => __('Partner Product Code'),
                'index' => 'vendor_product_code'
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price'
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        $vendorId = $this->getRequest()->getParam('sellerId');
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('storeclone/seller/storeproducts', ['_current' => true,'sellerId'=>$vendorId, 'id'=>$id]);
    }
}
