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

class StoreSearchResult extends Template
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
    protected $_template = 'customer/storesearchresult.phtml';

    /**
     * @var \Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Sellers
     */
    protected $blockGrid;

    /**
     * StoreSearchResult constructor.
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
     * @return string
     */
    public function getAjaxUrlStoreProducts()
    {
        $id = $this->getRequest()->getParam('id');
        return $this->getUrl('storeclone/seller/storeproducts',['id'=>$id]);
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
                'Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Sellers',
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
}