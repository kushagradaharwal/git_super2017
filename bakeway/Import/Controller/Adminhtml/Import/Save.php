<?php
/**
 *
 * Copyright © 2015 Bakewaycommerce. All rights reserved.
 */
namespace Bakeway\Import\Controller\Adminhtml\Import;

use Braintree\Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Bakeway\ProductApi\Helper\Data as ProductApiHelper;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Bakeway\Import\Helper\Data
     */
    protected $importhelper;
    /**
     *  \Webkul\Marketplace\Helper\Data
     */
    protected $webkulhelper;

    /**
     * @var ProductApiHelper
     */
    protected $productApiHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Bakeway\Import\Helper\Data $importhelper
     * @param \Webkul\Marketplace\Helper\Data $webkulhelper
     * @param ProductApiHelper $productApiHelper,
     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Bakeway\Import\Helper\Data $importhelper,
        \Webkul\Marketplace\Helper\Data $webkulhelper,
        ProductApiHelper $productApiHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_date = $date;
        $this->importhelper = $importhelper;
        $this->webkulhelper = $webkulhelper;
        $this->productApiHelper = $productApiHelper;
    }

    /**
     * @return get base url
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {


        if (!empty($_FILES['import_sellerinfo_file']['tmp_name'])) {
            $target_dir = BP . "/var/import/";

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . basename($_FILES["import_sellerinfo_file"]["name"]);

            $filepath = move_uploaded_file($_FILES['import_sellerinfo_file']['tmp_name'], $target_file);

            $filename = BP . '/var/import/' . $_FILES["import_sellerinfo_file"]["name"];

            $fp = fopen($filename, "r");
            $headerLine = true;
            $i = 1;
            while (($row = fgetcsv($fp, "5400", ",")) != FALSE) {
                if ($headerLine) {
                    $headerLine = false;
                } else {

                    $_CheckSellerFlag = $this->importhelper->getidFromEmail($row[1]);

                    if (empty($_CheckSellerFlag)) {
                        if (isset($row[1])) {
                            $firstname = "fname" . $i;
                            $lastname = "lname" . $i;
                            $data_string = array("customer" => array("firstname" => $firstname, "lastname" => $lastname, "email" => $row[1]),"password" =>$row[1]);
                            $ch = curl_init($this->getBaseUrl() . "/rest/V1/customers");
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_string));
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

                            $result = curl_exec($ch);
                            $result = json_decode($result, true); //get customer id from api response
                            $customerid = $result['id'];

                            $status = $this->_objectManager->get(
                                'Webkul\Marketplace\Helper\Data'
                            )->getIsPartnerApproval() ? 0 : 1;
                            $model = $this->_objectManager->create(
                                'Webkul\Marketplace\Model\Seller'
                            );
                            $paramData['is_seller'] = 1; //default 1
                            $paramData['profileurl'] = uniqid(rand(), true); //random id for shop url
                            $paramData['store_city'] = 1; //default city set to 0 = Pune
                            $model->setData('is_seller', $status);
                            $model->setData('shop_url', $paramData['profileurl']);
                            $model->setData('seller_id', $customerid);
                            $model->setData('store_city', $paramData['store_city']);
                            $model->setData('is_seller', 1);
                            $model->setCreatedAt($this->_date->gmtDate());
                            $model->setUpdatedAt($this->_date->gmtDate());
                            $model->setDelivery(1);
                            $Area = $row[0];
                            $Title = $row[2];
                            $Storeaddress = $row[3];
                            $Storelat = $row[4];
                            $StoreLong = $row[5];
                            $Storeomobile = $row[6];
                            $StoreMmobile = $row[7];

                            if (isset($Area)) {
                                $model->setStoreLocalityArea($Area);
                            }

                            if (isset($Title)) {
                              $_key =   $this->getBakeryName($Title);
                                $model->setShopTitle($Title);
                                $model->setBusinessName($Title);
                            }
                            if (isset($Storeaddress)) {
                                $model->setStoreStreetAddress($Storeaddress);
                            }
                            if (isset($Storelat)) {
                                $model->setStoreLatitude($Storelat);
                            }
                            if (isset($StoreLong)) {
                                $model->setStoreLongitude($StoreLong);
                            }
                            if (isset($Storeomobile)) {
                                $model->setStoreOwnerMobileNo($Storeomobile);
                            }
                            if (isset($StoreMmobile)) {
                                $model->setStoreManagerMobileNo($StoreMmobile);

                            }
                            try {
                                $model->save();
                                $this->productApiHelper->createVendorUrl($customerid);
                                $this->setLog("0" . $i . "@bakeway.com");
                            } catch (Exception $e) {
                                echo $e->getError();
                            }

                        }
                    } else {
                        $this->setLog("0" . $i . "@bakeway.com");

                    }
                }
                $i++;
            }

        }

        $this->messageManager->addSuccess(__('Seller has imported successfully !!!'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }


    public function setLog($email)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sellerdata.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($email);

    }

    /*
     * @return bakery id from bakery name
     */
    public function getBakeryName($name)
    {
        $_arrayBakery = [];
        $_collection = $this->webkulhelper->getBakeryTypeOptionArray();
        foreach ($_collection as $_bakery) {
            $_arrayBakery[] = $_bakery['label'];
        }
        $_getKey = array_search($name, $_arrayBakery);
        echo $_getKey;
    }
}
