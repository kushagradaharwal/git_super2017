<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Model;

use Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface;
use Bakeway\VendorNotification\Api\SellerdevicedataRepositoryInterface;
use Bakeway\VendorNotification\Model\Sellerdevicedata as Sellerdevicedata;
use Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata as Resource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Request;
use Bakeway\VendorNotification\Helper\Data;


class SellerdevicedataRepository implements SellerdevicedataRepositoryInterface
{
    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var Sellerdevicedata
     */
    protected $sellerdevicedata;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Sellerdevicedata $sellerdevicedata
     * @param Resource $resource
     * @param Request $request
     * @param Data $helper
     */
    public function __construct(
        Sellerdevicedata $sellerdevicedata,
        Resource $resource,
        Request $request,
        Data $helper
    )
    {
        $this->sellerdevicedata = $sellerdevicedata;
        $this->resource = $resource;
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * Create Seller Device Data.
     *
     * @api
     * @param \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData
     * @param int|null $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData, $sellerId = null)
    {
        try {
            $authorizationHeaderValue = $this->request->getHeader('Authorization');
            $headerPieces = explode(" ", $authorizationHeaderValue);
            $tokenType = strtolower($headerPieces[0]);
            $bearerToken = null;
            if ($tokenType === 'bearer') {
                $bearerToken = $headerPieces[1];
            }
            $deviceToken = $deviceData->getDeviceToken();
            $platform = $deviceData->getPlatform();

            $applicationArn = $this->helper->getPlatformApplicationArn($platform);

            $platformEndpoint = $this->helper->getPlatformEndpoint($applicationArn, $deviceToken);

            $isExisting = $this->sellerdevicedata->checkIsExisting($sellerId, $deviceToken, $platformEndpoint);

            if($isExisting === true || $platformEndpoint === false) {
                throw new AlreadyExistsException(__("Entry with same device_token and platform endpoint for the same seller already exist."));
            }
            $deviceData->setSellerId($sellerId);
            $deviceData->setToken($bearerToken);
            $deviceData->setPlatform($platform);
            $deviceData->setPlatformEndpoint($platformEndpoint);

            $this->resource->save($deviceData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $deviceData;
    }

    /**
     * Update Seller Device Data.
     *
     * @api
     * @param \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData
     * @param int|null $sellerId
     * @return \Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update(\Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface $deviceData, $sellerId = null){
        try {
            $deviceToken = $deviceData->getDeviceToken();
            $data = $this->sellerdevicedata->getByDeviceToken($sellerId, $deviceToken);
            $id = $data->getId();
            if($id){
                $authorizationHeaderValue = $this->request->getHeader('Authorization');
                $headerPieces = explode(" ", $authorizationHeaderValue);
                $tokenType = strtolower($headerPieces[0]);
                $bearerToken = null;
                if ($tokenType === 'bearer') {
                    $bearerToken = $headerPieces[1];
                }
                $data->setIsDnd($deviceData->getIsDnd());
                $data->setToken($bearerToken);
                $data->setSellerId($sellerId);
                $data->save();
                return $data;
            }else{
                throw new NoSuchEntityException(__('There is no entry with device_token "%1"',$deviceToken));
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }
}