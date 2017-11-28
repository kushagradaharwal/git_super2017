<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PayoutsCalculation
 * @author    Bakeway
 */

namespace Bakeway\PayoutsCalculation\Controller\Adminhtml\Export;

use Bakeway\PayoutsCalculation\Helper\Data as PayoutsHelper;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PayoutsHelper
     */
    protected $payoutsHelper;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PayoutsHelper $payoutsHelper
     * @param MarketplaceHelper $marketplaceHelper
     */
    public function __construct(
        Context $context,
        PayoutsHelper $payoutsHelper,
        MarketplaceHelper $marketplaceHelper
    ) {
        parent::__construct($context);
        $this->payoutsHelper = $payoutsHelper;
        $this->marketplaceHelper = $marketplaceHelper;
    }

    /**
     *
     */
    public function execute()
    {
        $result = [];
        $collection = $this->payoutsHelper->getUnpaidOrderCollection();
        foreach ($collection as $order) {
            $sellerId = $order->getData('seller_id');
            $isTaxPaidByBakeway = $order->getData('tax_paid_by_bakeway');
            $result[$sellerId]['sales_list_ids'][] = $order->getEntityId();
            if (!isset($result[$sellerId]['total_amount'])) {
                $result[$sellerId]['total_amount'] = 0;
            }
            if (!isset($result[$sellerId]['total_tax'])) {
                $result[$sellerId]['total_tax'] = 0;
            }
            if (!isset($result[$sellerId]['actual_seller_amount'])) {
                $result[$sellerId]['actual_seller_amount'] = 0;
            }
            if (!isset($result[$sellerId]['tcs_amount'])) {
                $result[$sellerId]['tcs_amount'] = 0;
            }
            if (!isset($result[$sellerId]['payment_gateway_fee'])) {
                $result[$sellerId]['payment_gateway_fee'] = 0;
            }
            if (!isset($result[$sellerId]['delivery_fee'])) {
                $result[$sellerId]['delivery_fee'] = 0;
            }
            if (!isset($result[$sellerId]['convenience_fee_incl_tax'])) {
                $result[$sellerId]['convenience_fee_incl_tax'] = 0;
            }
            if (!isset($result[$sellerId]['commission_incl_tax'])) {
                $result[$sellerId]['commission_incl_tax'] = 0;
            }
            if (!isset($result[$sellerId]['pending_amount'])) {
                $result[$sellerId]['pending_amount'] = 0;
            }
            if (!isset($result[$sellerId]['bakeway_amount'])) {
                $result[$sellerId]['bakeway_amount'] = 0;
            }
            if (!isset($result[$sellerId]['seller_account_number'])) {
                $result[$sellerId]['seller_account_number'] = $order->getData('store_owner_bank_account_number');
            }
            if (!isset($result[$sellerId]['seller_account_ifsc'])) {
                $result[$sellerId]['seller_account_ifsc'] = $order->getData('store_owner_bank_ifsc');
            }
            if (!isset($result[$sellerId]['seller_account_name'])) {
                $result[$sellerId]['seller_account_name'] = $order->getData('store_owner_bank_registered_name');
            }
            if (!isset($result[$sellerId]['seller_email'])) {
                $result[$sellerId]['seller_email'] = $order->getData('store_owner_email');
            }
            if (!isset($result[$sellerId]['seller_mobile_number'])) {
                $result[$sellerId]['seller_mobile_number'] = $order->getData('store_owner_mobile_no');
            }
            if (!isset($result[$sellerId]['nodal_account_number'])) {
                $result[$sellerId]['nodal_account_number'] = $this->payoutsHelper->getNodalAccountNumber();
            }
            if (!isset($result[$sellerId]['nodal_account_ifsc'])) {
                $result[$sellerId]['nodal_account_ifsc'] = $this->payoutsHelper->getNodalAccountIfsc();
            }
            if (!isset($result[$sellerId]['nodal_account_name'])) {
                $result[$sellerId]['nodal_account_name'] = $this->payoutsHelper->getNodalAccountName();
            }
            if (!isset($result[$sellerId]['credit_account_number'])) {
                $result[$sellerId]['credit_account_number'] = $this->payoutsHelper->getCreditAccountNumber();
            }
            if (!isset($result[$sellerId]['credit_account_name'])) {
                $result[$sellerId]['credit_account_name'] = $this->payoutsHelper->getCreditAccountName();
            }
            if (!isset($result[$sellerId]['credit_account_ifsc'])) {
                $result[$sellerId]['credit_account_ifsc'] = $this->payoutsHelper->getCreditAccountIfsc();
            }
            $result[$sellerId]['total_amount'] = $result[$sellerId]['total_amount'] + $order->getData('total_amount');
            $result[$sellerId]['total_tax'] = $result[$sellerId]['total_tax'] + $order->getData('total_tax');
            $result[$sellerId]['actual_seller_amount'] = $result[$sellerId]['actual_seller_amount']
                + $order->getData('actual_seller_amount');
            $result[$sellerId]['commission_incl_tax'] = $result[$sellerId]['commission_incl_tax']
                + $order->getData('commission_incl_tax');

            $isShipping = $order->getData('is_shipping');
            $bakewayShippingFeeExclTax = 0;
            if ($isShipping) {
                $result[$sellerId]['tcs_amount'] = $order->getData('tcs_amount');
                $result[$sellerId]['payment_gateway_fee'] = $order->getData('payment_gateway_fee');
                $result[$sellerId]['delivery_fee'] = $order->getData('delivery_fee');
                if ($isTaxPaidByBakeway == 1) {
                    $bakewayShippingFeeExclTax = $this->payoutsHelper->getDeliveryFeeExclTax($result[$sellerId]['delivery_fee']);
                    $bakewayShippingFeeExclTax = $result[$sellerId]['delivery_fee'] - $bakewayShippingFeeExclTax;
                }
                $result[$sellerId]['convenience_fee_incl_tax']  = $order->getData('convenience_fee_incl_tax');

                $result[$sellerId]['total_amount'] = $result[$sellerId]['total_amount']
                    + $result[$sellerId]['delivery_fee']
                    + $result[$sellerId]['convenience_fee_incl_tax'];
            }
            $taxToTheSeller = $order->getData('total_tax');
            /**
             * removing the total tax if paid by bakeway
             */
            if ($isTaxPaidByBakeway == 1) {
                $taxToTheSeller = 0;
            }
            $sellerAmount = $result[$sellerId]['actual_seller_amount']
                + $taxToTheSeller
                + $result[$sellerId]['delivery_fee']
                - $result[$sellerId]['tcs_amount']
                - $result[$sellerId]['payment_gateway_fee']
                - $bakewayShippingFeeExclTax;
            $result[$sellerId]['pending_amount'] = $sellerAmount;
        }

        if (!empty($result)) {
            foreach ($result as $sellerId=>$row) {
                $result[$sellerId]['bakeway_amount'] = $row['total_amount']
                    + $row['total_tax']
                    - $row['pending_amount'];
            }
        } else {
            $this->messageManager->addError(__('All pending payments already exported'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $url = $this->getUrl('payouts/export/index');
            $resultRedirect->setUrl($url);
            return $resultRedirect;
        }

        $fileName = $this->payoutsHelper->generateInwordExcelSheet($result);

        if ($fileName !== false) {
            $this->messageManager->addSuccess(__('Seller has imported successfully !!!'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $url = $this->getUrl('payouts/export/index',['file'=>$fileName]);
            $resultRedirect->setUrl($url);
        } else {
            $this->messageManager->addError(__('Error Creating a file. Try again later'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $url = $this->getUrl('payouts/export/index');
            $resultRedirect->setUrl($url);
        }
        return $resultRedirect;
    }
}