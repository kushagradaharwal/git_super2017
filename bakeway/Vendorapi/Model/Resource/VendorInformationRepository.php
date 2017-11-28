<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Model\Resource;


use Bakeway\Vendorapi\Api\VendorInformationRepositoryInterface;
use Webkul\Marketplace\Helper\Data as SellerHelper;
use \Bakeway\Cities\Helper\Data as BakewayCityHelper;

class VendorInformationRepository implements VendorInformationRepositoryInterface
{
    /**
     * @param SellerHelper $sellerHelper
     */
    protected $sellerHelper;

    /**
     * @var BakewayCityHelper
     */
    protected $bakewayCityHelper;

    /**
     * VendorInformationRepository constructor.
     * @param SellerHelper $sellerHelper
     * @param BakewayCityHelper $bakewayCityHelper
     */
    public function __construct(
        SellerHelper $sellerHelper,
        BakewayCityHelper $bakewayCityHelper
    )
    {
        $this->sellerHelper = $sellerHelper;
        $this->bakewayCityHelper = $bakewayCityHelper;
    }

    /**
     * @param int $vendorId
     * @return mixed
     */
    public function getDetails($vendorId)
    {
        $sellerInfo = $this->sellerHelper->getSellerDataBySellerId($vendorId)->load();
        $sellerData = $sellerInfo->getFirstItem()->getData();
        $sellerCity = $sellerInfo->getFirstItem()->getData('store_city');
        $result = [];
        $result[0] = $sellerData;
        $result[0]['store_city'] = $this->bakewayCityHelper->getCityNameById($sellerCity);
        return json_decode(json_encode($result[0], false));
    }
}