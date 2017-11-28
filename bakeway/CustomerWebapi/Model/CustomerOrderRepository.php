<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomerWebapi
 * @author    Bakeway
 */

namespace Bakeway\CustomerWebapi\Model;

use Bakeway\CustomerWebapi\Api\CustomerOrderRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Sales\Model\Order\Status as OrderStatus;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ResourceConnection;
use Bakeway\CustomAttributes\Helper\Data as CustomAttrHelper;

/**
 * Class CustomerOrderRepository
 * @package Bakeway\CustomerWebapi\Model
 */
class CustomerOrderRepository implements CustomerOrderRepositoryInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\Status
     */
    protected $orderStatus;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

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
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var CustomAttrHelper
     */
    protected $customAttrHelper;

    /**
     * VendorOrderRepository constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\Status
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder
     * @param InvoiceSender $invoiceSender
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param ResourceConnection $resourceConnection
     * @param CustomAttrHelper $customAttrHelper
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Status $orderStatus,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        InvoiceSender $invoiceSender,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ResourceConnection $resourceConnection,
        CustomAttrHelper $customAttrHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderStatus = $orderStatus;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->eventManager = $eventManager;
        $this->resourceConnection = $resourceConnection;
        $this->customAttrHelper = $customAttrHelper;
    }

    /**
     * Get Customer Order.
     *
     * @param int $customerId
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface Order interface.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder($customerId, $orderId)
    {
        $orderDetails = $this->orderRepository->get($orderId);
        return $orderDetails;
    }

    /**
     * Get Customer Order List.
     *
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface Order search result interface.
     */
    public function getCustomerOrderList($customerId, \Magento\Framework\Api\SearchCriteria $searchCriteria = null)
    {
        $order = $this->orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customerId)
            ->setOrder('created_at', 'desc');
        return $order;
    }

    /**
     * Get valid dates for order
     * @return array
     */
    public function getValidOrderDateList()
    {
        $result = [];
        $today = date("Y-m-d");
        for ($i=0; $i<10; $i++) {
            $result[$i]['label'] = date('F j, Y', strtotime($today .' +'.$i.' day'));
            $result[$i]['value'] = date('Y-m-d', strtotime($today .' +'.$i.' day'));
        }
        return json_decode(json_encode($result, false));
    }

    /**
     * Get valid time for order
     * @param string $sku
     * @return array
     */
    public function getValidOrderTimeList($sku) {
        $result = [];
        $timeArr = $this->customAttrHelper->getShopTimingsBySku($sku);
        $i=0;
        foreach ($timeArr as $timeElem) {
            $result[$i]['label'] = $timeElem;
            $result[$i]['value'] = $timeElem;
            $i++;
        }
        return $result;
    }
    
}