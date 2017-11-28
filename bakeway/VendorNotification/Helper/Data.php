<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Helper;

use Aws\Sns\SnsClient;
use Bakeway\VendorNotification\Model\Sellerdevicedata;
use Aws\Exception\AwsException;
use Webkul\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Bakeway VendorNotifcation Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const NEW_ORDER_PARTNER_SMS = 'Hi You have got the New order with ID : ';

    const NEW_ORDER_CUSTOMER_SMS = 'Thank you for placing the order. Your order ID is : ';

    const ORDER_STATUS_ACCEPTED_SMS = "Dear Customer Your Order has been accepted : ";

    const ORDER_STATUS_REJECTED_SMS = "Dear Customer Your Order has been REJECTED : ";

    const ORDER_STATUS_READY_SMS = "Dear Customer ORDER IS READY : ";

    const ORDER_STATUS_OUT_FOR_DELIVERY_SMS = "Dear Customer OUT FOR DELIVERY : ";

    const ORDER_STATUS_COMPLETED_SMS = "Dear Customer Your Order COMPLETED : ";

    /**
     * @var \Bakeway\VendorNotification\Model\Sellerdevicedata
     */
    protected $deviceData;

    /**
     * @var MarketplaceHelper
     */
    protected $marketplaceHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Bakeway\VendorNotification\Model\Sellerdevicedata $deviceData
     * @param MarketplaceHelper $marketplaceHelper
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Bakeway\VendorNotification\Model\Sellerdevicedata $deviceData,
        MarketplaceHelper $marketplaceHelper,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->deviceData = $deviceData;
        parent::__construct($context);
        $this->marketplaceHelper = $marketplaceHelper;
        $this->orderRepository = $orderRepository;
    }

    public function sendPushNotification($endpoints, $messageText, $orderId)
    {
        $adnroidArn = $this->getPlatformApplicationArn('android');
        $iosArn = $this->getPlatformApplicationArn('ios');
        $availableEndpointsAndr = $this->getAvailablePlatformEndpoints($adnroidArn);
        $availableEndpointsIos = $this->getAvailablePlatformEndpoints($iosArn);
        foreach ($endpoints as $endpoint) {
            try {
                $platform = $endpoint->getPlatform();
                $platformEndpoint = $endpoint->getPlatformEndpoint();
                $isEnabled = false;
                if ($platform == strtolower('ios')) {
                    if (in_array($platformEndpoint, $availableEndpointsIos)) {
                        $isEnabled = $this->checkEndpointEnabled($platformEndpoint);
                    }
                    $data = json_encode(array("aps" => array("alert" => $messageText, "title" => "Bakeway New Order", "sound"=>"new_order.aiff"), "order_id" => $orderId));
                    $push_message = json_encode(array("APNS" => $data));
                } else {
                    if (in_array($platformEndpoint, $availableEndpointsAndr)) {
                        $isEnabled = $this->checkEndpointEnabled($platformEndpoint);
                    }
                    $data = json_encode(array("data" => array("message" => $messageText, "title" => "Bakeway New Order", "order_id" => $orderId, "soundName"=>"new_order.mp3" )));
                    $push_message = json_encode(array("default" => "test", "GCM" => $data));
                }
                if ($isEnabled === false) {
                    $device = $this->deviceData->load($endpoint->getId());
                    $device->delete();
                } else {
                    $snsClient = $this->getSnsClient();
                    $result = $snsClient->publish(array('Message' => $push_message,
                        'TargetArn' => $endpoint->getPlatformEndpoint(), 'MessageStructure' => 'json'));
                }
            } catch (AwsException $e) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pushnoti_order.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());

            } catch (\Exception $e) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pushnoti_order.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
        return;
    }

    /**
     * @return string
     */
    public function getSnsClient()
    {
        $snsApiKey = $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/sns_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $snsApiSecrete = $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/sns_api_secrete', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $snsClient = new SnsClient(array(
            'credentials' => array('key' => $snsApiKey, 'secret' => $snsApiSecrete),
            'region' => 'ap-south-1',
            'version' => 'latest'
        ));

        return $snsClient;
    }

    /**
     * @param string $platform
     * @return string
     */
    public function getPlatformApplicationArn($platform)
    {
        if (strtolower($platform) == 'ios') {
            return $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/ios_platform_arn', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            return $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/android_platform_arn', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
    }

    /**
     * @param string $applicationArn
     * @param string $token
     * @return string|bool
     */
    public function getPlatformEndpoint($applicationArn, $token)
    {
        $snsClient = $this->getSnsClient();
        $result = $snsClient->createPlatformEndpoint(array(
            'PlatformApplicationArn' => $applicationArn,
            'Token' => $token
        ));
        if (isset($result['EndpointArn'])) {
            return $result['EndpointArn'];
        }
        return false;
    }

    /**
     * @param string $applicationArn
     * @return array
     */
    public function getAvailablePlatformEndpoints($applicationArn)
    {
        $endpoints = array();
        try{
            $snsClient = $this->getSnsClient();
            $result = $snsClient->listEndpointsByPlatformApplication(['PlatformApplicationArn' => $applicationArn]);

            if (isset($result['Endpoints']) && is_array($result['Endpoints'])) {
                $endpoints = array_column($result['Endpoints'], 'EndpointArn');
            }
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pushnoti_order.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }

        return $endpoints;
    }

    /**
     * @param string $endpoint
     * @return bool
     */
    public function checkEndpointEnabled($endpoint)
    {
        try {
            $snsClient = $this->getSnsClient();
            $result = $snsClient->getEndpointAttributes(['EndpointArn' => $endpoint]);

            if (isset($result['Attributes']) && $result['Attributes']['Enabled'] == "true") {
                return true;
            }
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pushnoti_order.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            return false;
        }
        return false;
    }

    /**
     * Get the SMS Client
     */
    public function getSmsClient() {
        $snsApiKey = $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/sns_api_key',
                                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $snsApiSecrete = $this->scopeConfig->getValue('vendor_app_settings/bakeway_general/sns_api_secrete',
                                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $smsClient = new SnsClient([
            'credentials' => ['key' => $snsApiKey, 'secret' => $snsApiSecrete],
            'region' => 'ap-southeast-1',
            'version' => 'latest'
        ]);

        return $smsClient;
    }

    /**
     * Send the SMS
     * @param int $mobileNumber
     * @param string $message
     */
    public function sendSms($mobileNumber, $message)
    {
        try {
            $smsClient = $this->getSmsClient();
            $msgAttributes = [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => 'BKW',
                ],
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional',
                ]
            ];

            $mobileNumber = $this->getNumberWithCode($mobileNumber);
            $smsClient->publish([
                'Message' => $message,
                'PhoneNumber' => $mobileNumber,
                'MessageStructure' => 'text',
                'MessageAttributes'=>$msgAttributes
            ]);
        } catch (AwsException $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ordersms.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ordersms.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }

    /**
     * Send sms to customer directly
     * @param int $mobileNumber
     * @param string $lastOrderId
     */
    public function sendNewOrderSmsToCustomer($mobileNumber, $lastOrderId)
    {
        $message = self::NEW_ORDER_CUSTOMER_SMS.$lastOrderId;
        if (isset($mobileNumber) && $mobileNumber != '') {
            $this->sendSms($mobileNumber, $message);
        }
    }

    /**
     * Get partner details and send sms
     * @param int $partnerId
     * @param string $lastOrderId
     */
    public function sendNewOrderSmsToPartner($partnerId, $lastOrderId)
    {
        $message = self::NEW_ORDER_PARTNER_SMS.$lastOrderId;
        $sellerObject = $this->marketplaceHelper->getSellerDataBySellerId($partnerId);
        $sellerData = $sellerObject->getFirstItem();
        if ($sellerData->getId()) {
            $ownerMobileNumber = $sellerData->getData('store_owner_mobile_no');
            if (isset($ownerMobileNumber) && $ownerMobileNumber != '') {
                $this->sendSms($ownerMobileNumber, $message);
            }
        }
    }

    /**
     * @param int $sellerId
     * @param string $lastOrderId
     */
    public function sendNewOrderSms($sellerId, $lastOrderId)
    {
        $order = $this->orderRepository->get($lastOrderId);

        if ($order->getId()) {
            $orderIncrementId = $order->getIncrementId();
            $this->sendNewOrderSmsToPartner($sellerId, $orderIncrementId);
            $customerMobile = $order->getBillingAddress()->getTelephone();
            if (isset($customerMobile)) {
                $this->sendNewOrderSmsToCustomer($customerMobile, $orderIncrementId);
            }
        }
    }

    /**
     * @param $mobileNumber
     * @return string
     */
    public function getNumberWithCode($mobileNumber)
    {
        if (strlen($mobileNumber) == 10) {
            return '91'.$mobileNumber;
        }

        if (strlen($mobileNumber) > 10) {
            return '91'.substr($mobileNumber, -10);
        }

        return $mobileNumber;
    }

    /**
     * @param $sellerId
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    public function sendOrderStatusChangeSms($sellerId, $order)
    {
        $status = $order->getStatus();
        $orderIncrementId = $order->getIncrementId();
        $customerMobile = $order->getBillingAddress()->getTelephone();
        if (!isset($customerMobile) || $customerMobile == '') {
            return;
        }
        switch ($status) {
            case \Bakeway\Vendorapi\Model\OrderStatus::STATUS_PARTNER_ACCEPTED :
                $message = self::ORDER_STATUS_ACCEPTED_SMS . $orderIncrementId;
                $this->sendSms($customerMobile, $message);
                break;
            case \Bakeway\Vendorapi\Model\OrderStatus::STATUS_PARTNER_REJECTED :
                $message = self::ORDER_STATUS_REJECTED_SMS . $orderIncrementId;
                $this->sendSms($customerMobile, $message);
                break;
            case \Bakeway\Vendorapi\Model\OrderStatus::STATUS_ORDER_READY :
                $message = self::ORDER_STATUS_READY_SMS . $orderIncrementId;
                $this->sendSms($customerMobile, $message);
                break;
            case \Bakeway\Vendorapi\Model\OrderStatus::STATUS_ORDER_OUT_FOR_DELIVERY :
                $message = self::ORDER_STATUS_OUT_FOR_DELIVERY_SMS . $orderIncrementId;
                $this->sendSms($customerMobile, $message);
                break;
            case \Bakeway\Vendorapi\Model\OrderStatus::STATUS_ORDER_COMPLETE :
                $message = self::ORDER_STATUS_COMPLETED_SMS . $orderIncrementId;
                $this->sendSms($customerMobile, $message);
                break;
        }
        return;
    }
}