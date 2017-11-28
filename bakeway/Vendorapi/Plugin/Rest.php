<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Plugin;

use Magento\Integration\Model\Oauth\Token;
use Webkul\Marketplace\Helper\Data as SellerHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class Rest
{
	/**
     * @var \Magento\Integration\Model\Oauth\Token
     */
    protected $tokenModel;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $sellerHelper;

	/**
     * Initialize dependencies
     *
     * @param \Magento\Integration\Model\Oauth\Token $tokenModel
     * @param \Webkul\Marketplace\Helper\Data $sellerHelper
     */
    public function __construct(
    		\Magento\Integration\Model\Oauth\Token $tokenModel,
    		SellerHelper $sellerHelper
    	)
    {
        $this->tokenModel = $tokenModel;
        $this->sellerHelper = $sellerHelper;
    }

    /**
     * Removing token access for the unapproved seller's.
     *
     * @param \Magento\Webapi\Controller\Rest\Interceptor $restInterceptor
     * @param \Magento\Framework\App\RequestInterface $request
     */
	public function beforeDispatch(\Magento\Webapi\Controller\Rest\Interceptor $restInterceptor, \Magento\Framework\App\RequestInterface $request) {
		$headerAuthToken = $request->getHeader('Authorization');
		if($headerAuthToken){
			$authTokenArray = explode(" ",$headerAuthToken);
			if(isset($authTokenArray[1])){
				$tokenString = $authTokenArray[1];
				$token = $this->tokenModel->loadByToken($tokenString);
				$customerId = $token->getCustomerId();
				if($customerId){
					$seller = $this->sellerHelper->getSellerDataBySellerId($token->getCustomerId())->load();
                    $sellerCount = $seller->count();
					$isApproved = $seller->getFirstItem()->getIsSeller();

					/*Removing the customer associated with token*/
					if($isApproved == 0 && $sellerCount > 0){
						$token->setCustomerId(Null);
						$token->save();
					}
				}
			}
		}
	}
}
