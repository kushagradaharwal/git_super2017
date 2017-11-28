<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Block\Adminhtml;

use \Magento\Backend\Block\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Webkul\Marketplace\Model\Product as VendorProduct;

class StoreProducts extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'customer/storeproducts.phtml';

    /**
     * @var \Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Products
     */
    protected $blockGrid;

    /**
     * StoreProducts constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve instance of grid block.
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Products',
                'seller.search.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block.
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return int
     */
    public function getSellerId()
    {
        return $this->getRequest()->getParam('sellerId');
    }

    /**
     * @return string
     */
    public function getAjaxUrlCloneProducts()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('storeclone/seller/storeclone',['id'=>$id]);
    }
}