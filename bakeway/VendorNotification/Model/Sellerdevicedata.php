<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Model;

use Magento\Framework\Model\AbstractModel;
use Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Vendor/Seller Device Data Model.
 *
 * @method \Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata _getResource()
 * @method \Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata getResource()
 */
class Sellerdevicedata extends AbstractModel implements SellerdevicedataInterface, IdentityInterface
{
    /**
     * Marketplace Seller cache tag.
     */
    const CACHE_TAG = 'bakeway_vendor_notification';

    /**
     * @var string
     */
    protected $_cacheTag = 'bakeway_vendor_notification';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bakeway_vendor_notification';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata');
    }

    /**
     * Create Seller Device Data.
     *
     * @param int $sellerId
     * @param string $deviceToken
     * @param string $bearerToken
     * @return boolean
     */
    public function checkIsExisting($sellerId, $deviceToken, $platformEndpoint)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('seller_id',$sellerId)
            ->addFieldToFilter('device_token',$deviceToken)
            ->addFieldToFilter('platform_endpoint',$platformEndpoint);
        $count = $collection->count();
        if($count > 0){
            return true;
        }
        return false;
    }

    /**
     * Create Seller Device Data.
     *
     * @param int $sellerId
     * @param string $deviceToken
     * @return boolean
     */
    public function getByDeviceToken($sellerId, $deviceToken)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('seller_id',$sellerId)
            ->addFieldToFilter('device_token',$deviceToken);
        return $collection->getFirstItem();
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSeller();
        }

        return parent::load($id, $field);
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Seller ID
     *
     * @return int|null
     */
    public function getSellerId()
    {
        return parent::getData(self::SELLER_ID);
    }

    /**
     * Set Seller ID
     *
     * @param int $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * Get Token
     *
     * @return string|null
     */
    public function getToken()
    {
        return parent::getData(self::TOKEN);
    }

    /**
     * Set Token
     *
     * @param string $token
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Get Device Registration ID From FCM/APNS
     *
     * @return string|null
     */
    public function getDeviceToken()
    {
        return parent::getData(self::DEVICE_TOKEN);
    }

    /**
     * Set Device Registration ID From FCM/APNS
     *
     * @param string $registrationId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setDeviceToken($registrationId)
    {
        return $this->setData(self::DEVICE_TOKEN, $registrationId);
    }

    /**
     * Get Platform Endpoint
     *
     * @return string|null
     */
    public function getPlatformEndpoint()
    {
        return parent::getData(self::PLATFORM_ENDPOINT);
    }

    /**
     * Set Platform Endpoint
     *
     * @param string $endpoint
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setPlatformEndpoint($endpoint)
    {
        return $this->setData(self::PLATFORM_ENDPOINT, $endpoint);
    }

    /**
     * Get Platform
     *
     * @return string|null
     */
    public function getPlatform()
    {
        return parent::getData(self::PLATFORM);
    }

    /**
     * Set Platform
     *
     * @param string $platform
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setPlatform($platform)
    {
        return $this->setData(self::PLATFORM, $platform);
    }

    /**
     * Get Is DND
     *
     * @return int|null
     */
    public function getIsDnd()
    {
        return parent::getData(self::IS_DND);
    }

    /**
     * Set Is DND
     *
     * @param int $id
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setIsDnd($id)
    {
        return $this->setData(self::IS_DND, $id);
    }

    /**
     * Device Data created date
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set Device Data created date
     *
     * @param string $createdAt
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Device Data updated date
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set Device Data updated date
     *
     * @param string $updatedAt
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
