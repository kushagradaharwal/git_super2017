<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorapi
 * @author    Bakeway
 */

namespace Bakeway\Vendorapi\Test\Api;

class VendorapiTest extends \Magento\TestFramework\TestCase\WebapiAbstract
{
    public function testBasicRoutingExplicitPath()
    {
        $vendorId = 1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/vendor/'.$vendorId.'/products',
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET,
            ]
        ];
        $requestData = ['vendorId' => $vendorId];
        $productList = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals('testProduct1', $productList['products'], "Item was retrieved unsuccessfully");
    }
} 