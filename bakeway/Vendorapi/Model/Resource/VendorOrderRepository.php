<?php

/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Model\Resource;

use Bakeway\Vendorapi\Api\VendorOrderRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Webkul\Marketplace\Controller\Adminhtml\Seller;
use Webkul\Marketplace\Model\Orders;
use Webkul\Marketplace\Model\Saleslist;
use Bakeway\Vendorapi\Model\OrderStatus as BakewayOrderStatus;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Sales\Model\Order\Status as OrderStatus;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\Exception\NoSuchEntityException;
use Webkul\Marketplace\Model\ResourceModel\Sellertransaction\CollectionFactory as SellerTransactionFactory;
use Magento\Framework\App\ResourceConnection;
use Bakeway\PayoutsCalculation\Helper\Data as PayoutHelper;
use Bakeway\PaymentsApi\Model\Capture as Captureorder;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;

/**
 * Class VendorOrderRepository
 * @package Bakeway\Vendorapi\Model\Resource
 */
class VendorOrderRepository implements VendorOrderRepositoryInterface {

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Webkul\Marketplace\Model\Orders
     */
    protected $vendorOrders;

    /**
     * @var \Webkul\Marketplace\Model\Saleslist
     */
    protected $saleslist;

    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    protected $orderStatus;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Bakeway\Vendorapi\Model\OrderStatus
     */
    protected $bakewayOrderStatus;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var SellerTransactionFactory
     */
    protected $sellerTransactionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var PayoutHelper
     */
    protected $payoutHelper;

    /**
     * @var Captureorder
     */
    protected $captureorder;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * VendorOrderRepository constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Webkul\Marketplace\Model\Orders
     * @param \Webkul\Marketplace\Model\Saleslist
     * @param \Magento\Sales\Model\Order\Status
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     * @param \Bakeway\Vendorapi\Model\OrderStatus
     * @param \Magento\Framework\Api\SearchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder
     * @param InvoiceSender $invoiceSender
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param SellerTransactionFactory $sellerTransactionFactory
     * @param ResourceConnection $resourceConnection
     * @param PayoutHelper $payoutHelper
     * @param Captureorder $captureorder
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Webkul\Marketplace\Model\Orders $vendorOrders,
        \Webkul\Marketplace\Model\Saleslist $salesList,
        \Magento\Sales\Model\Order\Status $orderStatus,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Bakeway\Vendorapi\Model\OrderStatus $bakewayOrderStatus,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        InvoiceSender $invoiceSender,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        SellerTransactionFactory $sellerTransactionFactory,
        ResourceConnection $resourceConnection,
        PayoutHelper $payoutHelper,
        Captureorder $captureorder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        MarketplaceHelper $marketplaceHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->vendorOrders = $vendorOrders;
        $this->salesList = $salesList;
        $this->orderStatus = $orderStatus;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->bakewayOrderStatus = $bakewayOrderStatus;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->eventManager = $eventManager;
        $this->sellerTransactionFactory = $sellerTransactionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->payoutHelper = $payoutHelper;
        $this->captureorder = $captureorder;
        $this->_messageManager = $messageManager;
        $this->marketplaceHelper = $marketplaceHelper;
    }

    /**
     * Get Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder($vendorId, $orderId) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);

        if ($vendorOrderId == $orderId) {
            $orderDetails = $this->orderRepository->get($orderId);
            return $orderDetails;
        } else {
            throw new NoSuchEntityException(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Accept Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function acceptOrder($vendorId, $orderId) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);

        if ($vendorOrderId == $orderId) {
            $order = $this->orderRepository->get($orderId);
            if ($order->getStatus() == "pending"):
                $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
                $order->setStatus(BakewayOrderStatus::STATUS_PARTNER_ACCEPTED);
                $order->addStatusToHistory($order->getStatus(), 'Order accepted by partner successfully.');
                $order->save();
                /*
                 * function for capturing order payment
                 */
                if ($order->getState() == \Magento\Sales\Model\Order::STATE_PROCESSING && $order->getStatus() == BakewayOrderStatus::STATUS_PARTNER_ACCEPTED):
                    $this->captureorder->capturePayment($orderId);
                endif;
                $this->eventManager->dispatch(
                        'bakeway_vendor_order_accept_after', ['order' => $order]
                );
                return $order;
            else:

                throw new CouldNotSaveException(__('This opration was not processed.Please refresh for Updated status'));
            endif;
        }else {
            throw new NoSuchEntityException(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Reject Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string|null $reason Order rejection reason
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function rejectOrder($vendorId, $orderId, $reason = null) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);

        if ($vendorOrderId == $orderId) {
            $order = $this->orderRepository->get($orderId);
            if ($order->getStatus() == "pending"):
                $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                $order->setStatus(BakewayOrderStatus::STATUS_PARTNER_REJECTED);
                if ($reason == null) {
                    $order->addStatusToHistory($order->getStatus(), 'Order rejected by partner.');
                } else {
                    $order->addStatusToHistory($order->getStatus(), $reason);
                }
                $order->save();
                return $order;
            else:
                throw new CouldNotSaveException(__('This opration was not processed.Please refresh for Updated status'));
            endif;
        } else {
            throw new NoSuchEntityException(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Get order statuses list for vendor.
     *
     * @param int|null $vendorId
     * @return array
     */
    public function getStatusList($vendorId = null) {
        $statusCollection = $this->orderStatus->getCollection()
                ->addFieldToSelect("*");
        $actionStatuses = $this->bakewayOrderStatus->actionStatusArray();
        $result = [];
        $i = 0;
        foreach ($statusCollection as $status) {
            $statusCode = $status->getStatus();
            if (in_array($statusCode, $actionStatuses)) {
                $result[$i]['status'] = $statusCode;
                $result[$i]['label'] = $status->getLabel();
                $result[$i]['action'] = true;
                $i++;
            }
        }
        return $result;
    }

    /**
     * Set Order status.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string $status_code The order status code
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function setOrderStatus($vendorId, $orderId, $status_code) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);
        $check = "";
        $actionStatuses = $this->bakewayOrderStatus->actionStatusArray();

        if ($vendorOrderId == $orderId) {

            if (in_array($status_code, $actionStatuses)) {
                $order = $this->orderRepository->get($orderId);
                $currentStatus = $order->getStatus();

                if ($status_code == BakewayOrderStatus::STATUS_ORDER_OUT_FOR_DELIVERY):
                    if ($currentStatus == BakewayOrderStatus::STATUS_PARTNER_ACCEPTED):
                        $check = true;
                    else:
                        $check = false;
                        throw new CouldNotSaveException(__('This opration was not processed.Please refresh for Updated status'));

                    endif;
                elseif ($status_code == BakewayOrderStatus::STATUS_ORDER_READY): 
                    if ($currentStatus == BakewayOrderStatus::STATUS_PARTNER_ACCEPTED):
                        $check = true;
                    else:
                        $check = false;
                        throw new CouldNotSaveException(__('This opration was not processed.Please refresh for Updated status'));
                    endif;
                elseif ($status_code == BakewayOrderStatus::STATUS_ORDER_COMPLETE):
                     
                     if ($currentStatus == BakewayOrderStatus::STATUS_ORDER_OUT_FOR_DELIVERY || 
                           $currentStatus == BakewayOrderStatus::STATUS_ORDER_READY  ):
                        $check = true;
                    else:
                        $check = false;
                        throw new CouldNotSaveException(__('This opration was not processed.Please refresh for Updated status'));
                    endif;
                endif;

                if($check === true):
                    if ($status_code == \Magento\Sales\Model\Order::STATE_COMPLETE) {
                        $this->generateInvoice($orderId, $vendorId);
                        $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
                    } else {
                        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                    }
                    $order->setStatus($status_code);
                    $order->addStatusToHistory($order->getStatus(), 'Order status changed.');
                    $order->save();
                    return $order;
                endif;
            } else {
                throw new CouldNotSaveException(__('Can not set the status as "%1" for this order.', $status_code));
            }
        } else {
            throw new NoSuchEntityException(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Get vendor order Id
     * @param int $vendorId
     * @param int $orderId
     * @return int
     */
    public function getVendorOrderId($vendorId, $orderId) {
        $vendorOrderCollection = $this->vendorOrders->getCollection()
                ->addFieldToFilter('seller_id', $vendorId)
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToSelect(['entity_id', 'order_id'])
                ->load();

        return $vendorOrderCollection->getFirstItem()->getOrderId();
    }

    /**
     * Get Vendor Order List.
     *
     * @param int $vendorId
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getVendorOrderList($vendorId, \Magento\Framework\Api\SearchCriteria $searchCriteria = null) {
        $vendorOrderCollection = $this->vendorOrders->getCollection()
                ->addFieldToFilter('seller_id', $vendorId)
                ->addFieldToSelect(['entity_id', 'order_id']);
        $orderIds = $vendorOrderCollection->getColumnValues('order_id');

        if (is_array($orderIds)) {
            /** creating filter to search order ids based on vendor order ids */
            $orderIdsFilter = $this->filterBuilder->setField('entity_id')
                    ->setValue($orderIds)
                    ->setConditionType('in')
                    ->create();
            $orderIdFilterGroup = $this->filterGroupBuilder
                    ->addFilter($orderIdsFilter)
                    ->create();


            if ($searchCriteria === null) {
                $searchCriteria = $this->searchCriteriaBuilder
                        ->setFilterGroups([$orderIdFilterGroup])
                        ->create();
            } else {
                $filterGroups = array();
                $filterGroups[] = $orderIdFilterGroup;
                foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                    $filterGroups[] = $filterGroup;
                }
                $searchCriteria->setFilterGroups($filterGroups);
            }


            $order = $this->orderRepository->getList($searchCriteria);
            return $order;
        }
    }

    /**
     * Get Vendor Unpaid Orders List.
     *
     * @param int $vendorId
     * @return array
     */
    public function getVendorUnpaidOrderList($vendorId) {
        /**
         * Joined sales_order, marketplace_orders and marketplace_saleslist tables
         */
        $orderCollection = $this->orderCollectionFactory->create()
                ->addFieldToSelect(['customer_email', 'customer_firstname', 'customer_lastname', 'customer_is_guest', 'created_at']);
        $orderCollection->getSelect()->joinInner(['mp_sl' => $orderCollection->getTable('marketplace_saleslist')], 'main_table.entity_id=mp_sl.order_id', ['entity_id', 'seller_id', 'order_id', 'paid_status', 'cpprostatus', 'total_amount',
            'actual_seller_amount', 'total_commission', 'magerealorder_id', 'commission_rate',
            'commission_tax_details', 'commission_incl_tax', 'is_shipping', 'total_tax'
        ]);
        $orderCollection->getSelect()->joinInner(['mp_order' => $orderCollection->getTable('marketplace_orders')], 'main_table.entity_id=mp_order.order_id', ['payment_gateway_fee', 'delivery_fee', 'tcs_amount', 'convenience_fee_incl_tax', 'tax_paid_by_bakeway']);
        $orderCollection->getSelect()->where('mp_sl.paid_status=?', 0);
        $orderCollection->getSelect()->where('mp_sl.cpprostatus=?', 1);
        $orderCollection->getSelect()->where('mp_sl.parent_item_id IS NULL');
        $orderCollection->getSelect()->where('mp_sl.seller_id=?', $vendorId);

        /**
         * Code to club sales list items into one order.
         */
        $result = array();
        $i = 0;
        $pendingOrders = [];
        $totalPendingAmount = 0;
        /**
         * Declared variables to store last item values from saleslist
         */
        $lastOrderId = 0;
        $lastTcsFee = 0;
        $lastDeliveryFee = 0;
        $lastConvenienceFee = 0;
        $lastPgFee = 0;
        $lastTotalTax = 0;
        $lastTotalAmount = 0;
        $lastTotalCommission = 0;
        $lastItemCommissionCgst = 0;
        $lastItemCommissionSgst = 0;
        $lastItemCommissionIgst = 0;
        $lastActualSellerAmount = 0;
        $lastTotalAmountInclTax = 0;

        foreach ($orderCollection as $order) {
            $orderId = $order->getData('order_id');
            $isTaxPaidByBakeway = $order->getData('tax_paid_by_bakeway');
            if ($lastOrderId == $orderId) {
                $i--;
            }
            $pendingOrders[$i]['customer_email'] = $order->getData('customer_email');
            $pendingOrders[$i]['customer_firstname'] = $order->getData('customer_firstname');
            $pendingOrders[$i]['customer_lastname'] = $order->getData('customer_lastname');
            $pendingOrders[$i]['customer_is_guest'] = $order->getData('customer_is_guest');
            $pendingOrders[$i]['created_at'] = $order->getData('created_at');
            $pendingOrders[$i]['seller_id'] = $order->getData('seller_id');
            $pendingOrders[$i]['order_id'] = $order->getData('order_id');
            $pendingOrders[$i]['magerealorder_id'] = $order->getData('magerealorder_id');
            $pendingOrders[$i]['paid_status'] = $order->getData('paid_status    ');
            $pendingOrders[$i]['cpprostatus'] = $order->getData('cpprostatus');
            $pendingOrders[$i]['commission_rate'] = number_format($order->getData('commission_rate'), 2, '.', '');


            if ($lastOrderId != $orderId) {
                $lastTcsFee = 0;
                $lastDeliveryFee = 0;
                $lastConvenienceFee = 0;
                $lastPgFee = 0;
                $lastTotalTax = 0;
                $lastTotalAmount = 0;
                $lastTotalCommission = 0;
                $lastItemCommissionCgst = 0;
                $lastItemCommissionSgst = 0;
                $lastItemCommissionIgst = 0;
                $lastActualSellerAmount = 0;
                $lastTotalAmountInclTax = 0;
            }

            $isShipping = $order->getData('is_shipping');
            $deliveryFee = 0;
            $tcsFee = 0;
            $pgFee = 0;
            $convenienceFee = 0;
            $bakewayShippingFeeExclTax = 0;

            $totalTax = $order->getData('total_tax') + $lastTotalTax;
            $totalAmount = $order->getData('total_amount') + $lastTotalAmount;
            $totalAmount = $totalAmount + $order->getData('total_tax');
            $totalAmountInclTax = $order->getData('total_amount') + $order->getData('total_tax') + $lastTotalAmountInclTax;
            $sellerAmount = $order->getData('actual_seller_amount');

            if ($isShipping) {
                $tcsFee = $order->getData('tcs_amount');
                $lastTcsFee = $tcsFee;
                $pgFee = $order->getData('payment_gateway_fee');
                $lastPgFee = $pgFee;
                $deliveryFee = $order->getData('delivery_fee');
                if ($isTaxPaidByBakeway == 1) {
                    $bakewayShippingFeeExclTax = $this->payoutHelper->getDeliveryFeeExclTax($deliveryFee);
                    $bakewayShippingFeeExclTax = $deliveryFee - $bakewayShippingFeeExclTax;
                }
                $lastDeliveryFee = $deliveryFee;
                $convenienceFee = $order->getData('convenience_fee_incl_tax');
                $lastConvenienceFee = $convenienceFee;

                $totalAmount = $totalAmount + $deliveryFee + $convenienceFee;
            }
            $itemCommissionCgst = 0;
            $itemCommissionSgst = 0;
            $itemCommissionIgst = 0;
            $commissionCgstPercent = 0;
            $commissionSgstPercent = 0;
            $commissionIgstPercent = 0;
            $taxDetailsArr = [];
            $taxDetailsString = $order->getData('commission_tax_details');
            if ($taxDetailsString != Null) {
                $taxDetailsArr = unserialize($taxDetailsString);
            }
            foreach ($taxDetailsArr as $taxDetails) {
                if (strtolower($taxDetails['label']) == strtolower('cgst')) {
                    $itemCommissionCgst = $taxDetails[0]['value'] + $lastItemCommissionCgst;
                    $commissionCgstPercent = $taxDetails[0]['percentage'];
                } elseif (strtolower($taxDetails['label']) == strtolower('sgst')) {
                    $itemCommissionSgst = $taxDetails[0]['value'] + $lastItemCommissionSgst;
                    $commissionSgstPercent = $taxDetails[0]['percentage'];
                } elseif (strtolower($taxDetails['label']) == strtolower('igst')) {
                    $itemCommissionIgst = $taxDetails[0]['value'] + $lastItemCommissionIgst;
                    $commissionIgstPercent = $taxDetails[0]['percentage'];
                }
            }
            $pendingOrders[$i]['commission_cgst'] = number_format($itemCommissionCgst, 2, '.', '');
            $pendingOrders[$i]['commission_cgst_percent'] = number_format($commissionCgstPercent, 2, '.', '');
            $pendingOrders[$i]['commission_sgst'] = number_format($itemCommissionSgst, 2, '.', '');
            $pendingOrders[$i]['commission_sgst_percent'] = number_format($commissionSgstPercent, 2, '.', '');
            $pendingOrders[$i]['commission_igst'] = number_format($itemCommissionIgst, 2, '.', '');
            $pendingOrders[$i]['commission_igst_percent'] = number_format($commissionIgstPercent, 2, '.', '');
            $pendingOrders[$i]['total_commission'] = number_format($order->getData('commission_incl_tax') + $lastTotalCommission, 2, '.', '');

            $taxToTheSeller = $order->getData('total_tax');
            /**
             * removing the total tax if paid by bakeway
             */
            if ($isTaxPaidByBakeway == 1) {
                $taxToTheSeller = 0;
            }
            $sellerAmount = $sellerAmount + $taxToTheSeller + $deliveryFee - $tcsFee - $pgFee + $lastActualSellerAmount - $bakewayShippingFeeExclTax;
            $pendingOrders[$i]['delivery_fee'] = number_format($lastDeliveryFee, 2, '.', '');
            $pendingOrders[$i]['convenience_fee'] = number_format($lastConvenienceFee, 2, '.', '');
            $pendingOrders[$i]['actual_seller_amount'] = number_format($sellerAmount, 2, '.', '');
            $pendingOrders[$i]['payment_gateway_fee'] = number_format($lastPgFee, 2, '.', '');
            $pendingOrders[$i]['tax_collected_at_source'] = number_format($lastTcsFee, 2, '.', '');
            $pendingOrders[$i]['total_tax'] = number_format($totalTax, 2, '.', '');
            $pendingOrders[$i]['total_amount'] = number_format($totalAmount, 2, '.', '');
            $pendingOrders[$i]['base_order_cost'] = number_format($totalAmountInclTax, 2, '.', '');

            if ($lastOrderId != $orderId) {
                $totalPendingAmount = $totalPendingAmount + $sellerAmount;
            } else {
                $totalPendingAmount = $totalPendingAmount + $sellerAmount - $lastActualSellerAmount;
            }

            $lastTotalTax = $totalTax;
            $lastTotalAmount = $totalAmount;
            $lastTotalCommission = $order->getData('commission_incl_tax') + $lastTotalCommission;
            $lastItemCommissionCgst = $itemCommissionCgst;
            $lastItemCommissionSgst = $itemCommissionSgst;
            $lastItemCommissionIgst = $itemCommissionIgst;
            $lastActualSellerAmount = $sellerAmount;
            $lastTotalAmountInclTax = $totalAmountInclTax;
            $lastOrderId = $orderId;

            $i++;
        }

        $lastTransAmountPaid = 0;
        $lastTransDate = null;
        $transCollection = $this->sellerTransactionFactory->create()
                        ->addFieldToFilter("seller_id", $vendorId)
                        ->setOrder('created_at', 'desc')
                        ->load()->getFirstItem();
        if ($transCollection->getEntityId()) {
            $lastTransAmountPaid = $transCollection->getData('transaction_amount');
            $lastTransDate = $transCollection->getData('created_at');
        }

        $result['pending_orders'] = $pendingOrders;
        $result['total_pending_amount'] = number_format($totalPendingAmount, 2, '.', '');
        $result['last_payment_received_date'] = $lastTransDate;
        $result['last_payment_received'] = number_format($lastTransAmountPaid, 2, '.', '');
        return json_decode(json_encode($result, false));
    }

    /**
     * Generate invoice for order
     * @param int $orderId
     * @param int $vendorId
     * @return void
     */
    public function generateInvoice($orderId, $vendorId) {
        $isGstin = true;
        $sellerCollection = $this->marketplaceHelper->getSellerDataBySellerId($vendorId);
        if ($sellerCollection->count() > 0) {
            $seller = $sellerCollection->getFirstItem();
            if (
                !($seller->getData('userdata_gstin_number')) ||
                $seller->getData('userdata_gstin_number') == null ||
                trim($seller->getData('userdata_gstin_number')) == ''
            ) {
                $isGstin = false;
            }
        }
        if ($isGstin === false) {
            $order = $this->orderRepository->get($orderId);
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->transaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();
                $this->invoiceSender->send($invoice);
                //TODO : add code to send notification to customer
                $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice->getId()))
                    ->setIsCustomerNotified(true)
                    ->save();
            }
        }
    }

    /**
     * Get order rejected reasons list for vendor.
     *
     * @param int $vendorId
     * @return array
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function getRejectedReasonsList($vendorId) {
        $connection = $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $table = $connection->getTableName('bakeway_order_rejected_reasons');
        $query = 'SELECT reason_content FROM `' . $table . '` WHERE is_for_seller=1 AND is_active=1';
        $result = $connection->fetchAll($query);
        return json_decode(json_encode($result, false));
    }

    /*
     * vendor app status code
     */

    /**
     * Accept Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function vendoracceptOrder($vendorId, $orderId) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);

        if ($vendorOrderId == $orderId) {
            $order = $this->orderRepository->get($orderId);
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
            $order->setStatus(BakewayOrderStatus::STATUS_PARTNER_ACCEPTED);
            $order->addStatusToHistory($order->getStatus(), 'Order accepted by partner successfully.');
            $order->save();

            /*
             * function for capturing order payment
             */
            if ($order->getState() == \Magento\Sales\Model\Order::STATE_PROCESSING && $order->getStatus() == BakewayOrderStatus::STATUS_PARTNER_ACCEPTED):
                $this->captureorder->capturePayment($orderId);
            endif;
            $this->eventManager->dispatch(
                    'bakeway_vendor_order_accept_after', ['order' => $order]
            );
            return $order;
        }else {
            throw new NoSuchEntityException(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Reject Vendor Order.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string|null $reason Order rejection reason
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function vendorrejectOrder($vendorId, $orderId, $reason = null) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);

        if ($vendorOrderId == $orderId) {
            $order = $this->orderRepository->get($orderId);
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
            $order->setStatus(BakewayOrderStatus::STATUS_PARTNER_REJECTED);
            if ($reason == null) {
                $order->addStatusToHistory($order->getStatus(), 'Order rejected by partner.');
            } else {
                $order->addStatusToHistory($order->getStatus(), $reason);
            }
            $order->save();
            $this->_messageManager->addSuccess('Order rejected by partner.');
            return $order;
        } else {
            $this->_messageManager->addError(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

    /**
     * Set Order status.
     *
     * @param int $vendorId
     * @param int $orderId The order ID.
     * @param string $status_code The order status code
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function vendorsetOrderStatus($vendorId, $orderId, $status_code) {
        $vendorOrderId = $this->getVendorOrderId($vendorId, $orderId);
        $actionStatuses = $this->bakewayOrderStatus->actionStatusArray();
        if ($vendorOrderId == $orderId) {
            if (in_array($status_code, $actionStatuses)) {
                $order = $this->orderRepository->get($orderId);
                if ($status_code == \Magento\Sales\Model\Order::STATE_COMPLETE) {
                    $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
                } else {
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
                }
                $order->setStatus($status_code);
                $order->addStatusToHistory($order->getStatus(), 'Order status changed.');
                $order->save();
                $this->_messageManager->addSuccess('Order status changed.');
            } else {
                $this->_messageManager->addError(__('Can not set the status as "%1" for this order.', $status_code));
            }
        } else {
            $this->_messageManager->addError(__('There is no order with number "%1" is associated with this vendor.', $orderId));
        }
    }

}
