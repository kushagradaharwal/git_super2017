<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Api\Data;

/**
 * Seller device data interface.
 * @api
 */
interface SellerdevicedataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID    = 'id';

    const SELLER_ID = 'seller_id';

    const TOKEN = 'token';

    const DEVICE_TOKEN = 'device_token';

    const PLATFORM_ENDPOINT = 'platform_endpoint';

    const PLATFORM = 'platform';

    const IS_DND = 'is_dnd';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setId($id);

    /**
     * Get Seller ID
     *
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set Seller ID
     *
     * @param int $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get Token
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Set Token
     *
     * @param string $token
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setToken($token);

    /**
     * Get Device Registration ID From FCM/APNS
     *
     * @return string|null
     */
    public function getDeviceToken();

    /**
     * Set Device Registration ID From FCM/APNS
     *
     * @param string $registrationId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setDeviceToken($registrationId);

    /**
     * Get Platform Endpoint
     *
     * @return string|null
     */
    public function getPlatformEndpoint();

    /**
     * Set Platform Endpoint
     *
     * @param string $endpoint
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setPlatformEndpoint($endpoint);

    /**
     * Get Platform
     *
     * @return string|null
     */
    public function getPlatform();

    /**
     * Set Platform
     *
     * @param string $platform
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setPlatform($platform);

    /**
     * Get Is DND
     *
     * @return int|null
     */
    public function getIsDnd();

    /**
     * Set Is DND
     *
     * @param int $id
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setIsDnd($id);

    /**
     * Device Data created date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Device Data created date
     *
     * @param string $createdAt
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Device Data updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Device Data updated date
     *
     * @param string $updatedAt
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     */
    public function setUpdatedAt($updatedAt);
}
