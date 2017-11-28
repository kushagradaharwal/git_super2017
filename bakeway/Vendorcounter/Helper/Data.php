<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Helper;

/**
 * Bakeway Vendorcounter Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
    }

    /**
     * Get Seller Id by Order Id
     * @param int $orderId
     * @return int
     */
    public function getSellerIdByOrderId($orderId)
    {
        $sellerId = 0;
        $orders = $this->objectManager->create(
            'Webkul\Marketplace\Model\Order'
        )
            ->getCollection()
            ->addFieldToSelect(['seller_id'])
            ->addFieldToFilter(
                'order_id',
                $orderId
            )->load();
        
        if($orders->count() > 0) {
            $sellerId = $orders->getFirstItem()->getSellerId();
        }

        return $sellerId;
    }

    /**
     * Get Shop Data By Seller Id
     * @param int $sellerId
     * @return \Bakeway\Shopapi\Model\CustomShop|bool
     */
    public function getShopDataBySellerId($sellerId)
    {
        $currentDate = date('Y/m/d');
        $shopModel = $this->objectManager->create(
            'Bakeway\Shopapi\Model\CustomShop'
        )
            ->getCollection()
            ->addFieldToFilter('date', $currentDate)
            ->addFieldToFilter('seller_id', $sellerId)->load();

        if($shopModel->count() > 0) {
            $shopData = $shopModel->getFirstItem()->getSellerId();
            return $shopData;
        }
        return false;
    }
}