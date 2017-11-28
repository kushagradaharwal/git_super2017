<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */
namespace Bakeway\Vendorapi\Model\Rewrite\Order\Pdf;

use Bakeway\Cities\Helper\Data as CitiesHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    /**
     * @var CitiesHelper
     */
    protected $citiesHelper;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param MarketplaceHelper $marketplaceHelper
     * @param CitiesHelper $citiesHelper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        MarketplaceHelper $marketplaceHelper,
        CitiesHelper $citiesHelper,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->citiesHelper = $citiesHelper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Custom data array for bakeway */
            $pdfData = $this->getPdfData($order);
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());



            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());

            $this->insertPdfData($page, $pdfData);

            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * @param $order
     * @return array
     */
    public function getPdfData($order)
    {
        $bakewayPan = $this->scopeConfig->getValue('payouts_calculation/bakeway_account_details/bakeway_pan',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $bakewayGstin = $this->scopeConfig->getValue('payouts_calculation/bakeway_account_details/bakeway_gstin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $sellerId = null;
        $businessName = '';
        /** @var \Magento\Framework\App\Action\Action $controller */
        $collection = $this->objectManager->create('Webkul\Marketplace\Model\Orders')
            ->getCollection()
            ->addFieldToFilter('order_id', $order->getId());
        foreach ($collection as $mpOrder) {
            $sellerId = $mpOrder->getData('seller_id');
            $sellerColl = $this->marketplaceHelper->getSellerDataBySellerId($sellerId);
            if ($sellerColl->count() > 0) {
                $seller = $sellerColl->getFirstItem();
                $businessName = $seller->getData('business_name');
                $sellerAddress = $seller->getData('store_locality_area');
                $cityId = $seller->getData('store_city');
                $cityName = $this->citiesHelper->getCityNameById($cityId);
                $sellerAddress = $sellerAddress.', '.$cityName;
            }
        }
        return [
            'businessName' => $businessName,
            'sellerAddress' => $sellerAddress,
            'bakewayPan' => $bakewayPan,
            'bakewayGstin' => $bakewayGstin
        ];
    }
    
    public function insertPdfData($page, $pdfData) {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawText('Seller: '.$pdfData['businessName'], 350, 805 - 15, 'UTF-8');
        $page->drawText($pdfData['sellerAddress'], 350, 792 - 15, 'UTF-8');
        $page->drawText('PAN: '.$pdfData['bakewayPan'], 350, 777 - 15, 'UTF-8');
        $page->drawText('GSTIN: '.$pdfData['bakewayGstin'], 350, 764 - 15, 'UTF-8');
    }
}