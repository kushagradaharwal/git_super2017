

http://vinaikopp.com/2017/02/18/magento2_repositories_interfaces_and_webapi/

classed and objects  *****
http://phpenthusiast.com/object-oriented-php-tutorials/create-classes-and-objects

for magento2 coding
http://www.ibnab.com/en/blog/magento-2/track-v1-rest-url-how-you-can-explore-the-path-of-request

for m2 rest web api 
http://vinaikopp.com/2017/02/18/magento2_repositories_interfaces_and_webapi/
http://www.dckap.com/blog/magento-2-webapi/


for using jquery 
http://ashutoshpandey.in/using-requirejs-in-magento2/


pay you money
https://documentation.payubiz.in/hosted-page-copy/


custom extension attribute
https://web4pro.net/blog-news/magento-2-add-custom-attribute-customers-address/

for event file and event list
https://cyrillschumacher.com/magento2-list-of-all-dispatched-events/


https://www.packtpub.com/books/content/managing-payment-and-shipping-magento-2
read this 

event list


https://magento.stackexchange.com/questions/101152/what-are-the-main-tables-that-updated-when-we-creating-new-product-in-magento
have to read about to know database 
	
magento 2 import blog
https://www.ashsmith.io/magento2/module-from-scratch-module-part-2-models/

api
http://vinaikopp.com/2017/02/18/magento2_repositories_interfaces_and_webapi/
http://codetheatres.com/magento/creating-custom-api-in-magento2-using-rest-protocol/
https://www.thirdandgrove.com/creating-custom-rest-api-magento2
<?php
return $this->_redirect('deliveryrangeprice/delivery/rangeprice');
?>

<?php

REATE DATABASE IF NOT EXISTS dbdemo;
 
USE dbdemo;
 
CREATE TABLE categories(
   cat_id int not null auto_increment primary key,
   cat_name varchar(255) not null,
   cat_description text
) ENGINE=InnoDB;
 
CREATE TABLE products(
   prd_id int not null auto_increment primary key,
   prd_name varchar(355) not null,
   prd_price decimal,
   cat_id int not null,
   FOREIGN KEY fk_cat(cat_id)
   REFERENCES categories(cat_id)
   ON UPDATE CASCADE
   ON DELETE RESTRICT
)ENGINE=InnoDB

?>


A foreign key is a field in a table that matches another field of another table. 
A foreign key places constraints on data in the related tables, which enables MySQL to maintain referential integrity.

A table may have more than one foreign key, and each foreign key in the child table may refer to a different parent table.

*A foreign key is a key used to link two tables together. This is sometimes also called as a referencing key.

A Foreign Key is a column or a combination of columns whose values match a Primary Key in a different table.
first table  : primary key 
second table  : foregin  key

---------------------------------------------------------------------------------------------------------
phtml confirmation box
require([
            'jquery',
            'Magento_Ui/js/modal/confirm'
        ],
        function ($, confirmation) {
        $('.cancel').click(function (event) {
            event.preventDefault();

            var url = event.currentTarget.href;
            confirmation({
                title: 'Cancel order',
                content: 'Do you wish to cancel this order?',
                actions: {
                    confirm: function () {
                        window.location.href = url;
                    },
                    cancel: function () {},
                    always: function () {}
                }
            });
            return false;
        });
    });
---------------------------------------------------------------------------------------------------------	
curl -X POST "https://magento.host/index.php/rest/V1/integration/customer/token" \
     -H "Content-Type:application/json" \
     -d "{"username":"customer1@example.com", "password":"customer1pw"}"

---------------------------------------------------------------------------------------------------------
postman api 

body  : {
"username": "kush@relfor.com",
"password": "Kush123!@#"
}

json: application/json	 

---------------------------------------------------------------------------------------------------------
<button class="button wk-mp-btn" title="<?php echo __('Create Invoice to confirm collected amount from buyer for this order') ?>" onclick="return confirm('<?php echo __("Are you sure you want to create invoice?") ?>')" type="button">
---------------------------------------------------------------------------------------------------------
    inside body to give title of any page
	<referenceBlock name="page.main.title">
            <action method="setPageTitle">
                <argument translate="true" name="title" xsi:type="string">Marketplace Add New Product</argument>
            </action>
        </referenceBlock>
---------------------------------------------------------------------------------------------------------	

<head>
        <css src="Webkul_Marketplace::css/wk_block.css"/>
        <css src="Webkul_Marketplace::css/style.css"/>
    </head>
frontend/web/css	
	
---------------------------------------------------------------------------------------------------------	
	$this->_redirect('customer/account/');

---------------------------------------------------------------------------------------------------------
default customer id
<?php
    $customerSession = $om->get('Magento\Customer\Model\Session');
    if($customerSession->isLoggedIn()) {
        echo   $customerSession->getCustomer()->getId()."<br/>";  // get Customer Id
        echo   $customerSession->getCustomer()->getName()."<br/>";  // get  Full Name
        echo   $customerSession->getCustomer()->getEmail()."<br/>"; // get Email Name
        echo   $customerSession->getCustomer()->getGroupId()."<br/>";  // get Customer Group Id
      }  
?>

---------------------------------------------------------------------------------------------------------
event magento	
   <event name="controller_action_predispatch">
        <observer name="customer_visitor" instance="Magento\Customer\Observer\Visitor\InitByRequestObserver" />
    </event>
	use Magento\Framework\Event\Observer
	(Observer $observer)
	
---------------------------------------------------------------------------------------------------------
  inside di.xml
  
  <type name="Magento\Customer\Controller\Account\LoginPost">
        <plugin name="CustomloginPost" type="Bakeway\EventsListing\Plugin\Customer\LoginPost" sortOrder="10"
                disabled="false"/>
    </type>
	
	   public function afterExecute(\Magento\Customer\Controller\Account\LoginPost $subject, $result)
    {
		
---------------------------------------------------------------------------------------------------------
Magento\Sales\Model\Order\Status
<?php
   public function assignState($state, $isDefault = false, $visibleOnFront = false)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Status $resource */
        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->assignState($this->getStatus(), $state, $isDefault, $visibleOnFront);
            $resource->commit();
        } catch (\Exception $e) {
            $resource->rollBack();
            throw $e;
        }
        return $this;
    }
?>

php regular expression
Using regular expression you can search a particular string inside a another string, you can replace one string by another string and you can split a string into many chunks.

example : preg_match
---------------------------------------------------------------------------------------------------------
 $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pushnoti_order.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
---------------------------------------------------------------------------------------------------------			

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::KEY_CITY);
    }
    /**
     * Set city
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::KEY_CITY, $city);
    }
	
---------------------------------------------------------------------------------------------------------	
set page title 
	    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Commission'));
        return parent::_prepareLayout();
    }

---------------------------------------------------------------------------------------------------------
magento2 install
php bin/magento setup:install --base-url=http://127.0.0.1/mg1.8 --db-host=localhost --db-name=mg2.1.8 --db-user=root --db-password= --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com --admin-user=admin --admin-password=admin123 --language=en_US --currency=USD --timezone=America/Chicago --use-rewrites=1
---------------------------------------------------------------------------------------------------------
Media query
/*
  Based on:
  1. http://stephen.io/mediaqueries
  2. https://css-tricks.com/snippets/css/media-queries-for-standard-devices/
*/

/* iPhone 6 in portrait & landscape */
@media only screen 
and (min-device-width : 375px) 
and (max-device-width : 667px) {
  
}

/* iPhone 6 in landscape */
@media only screen 
and (min-device-width : 375px) 
and (max-device-width : 667px) 
and (orientation : landscape) {
  
}

/* iPhone 6 in portrait */
@media only screen 
and (min-device-width : 375px) 
and (max-device-width : 667px) 
and (orientation : portrait) {
  
}

/* iPhone 6 Plus in portrait & landscape */
@media only screen 
and (min-device-width : 414px) 
and (max-device-width : 736px) {
  
}

/* iPhone 6 Plus in landscape */
@media only screen 
and (min-device-width : 414px) 
and (max-device-width : 736px) 
and (orientation : landscape) {
  
}

/* iPhone 6 Plus in portrait */
@media only screen 
and (min-device-width : 414px) 
and (max-device-width : 736px) 
and (orientation : portrait) {
  
}

/* iPhone 5 & 5S in portrait & landscape */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 568px) {
  
}

/* iPhone 5 & 5S in landscape */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 568px) 
and (orientation : landscape) {
  
}

/* iPhone 5 & 5S in portrait */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 568px) 
and (orientation : portrait) {
  
}

/* 
  iPhone 2G, 3G, 4, 4S Media Queries
  It's noteworthy that these media queries are also the same for iPod Touch generations 1-4.
*/

/* iPhone 2G-4S in portrait & landscape */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) {
  
}

/* iPhone 2G-4S in landscape */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) 
and (orientation : landscape) {
  
}

/* iPhone 2G-4S in portrait */
@media only screen 
and (min-device-width : 320px) 
and (max-device-width : 480px) 
and (orientation : portrait) {
  
}

/* iPad in portrait & landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px)  {
  
}

/* iPad in landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : landscape) {
  
}

/* iPad in portrait */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : portrait) {
  
}

/* Galaxy S3 portrait and landscape */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 2) {

}

/* Galaxy S3 portrait */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 2) 
  and (orientation: portrait) {

}

/* Galaxy S3 landscape */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 2) 
  and (orientation: landscape) {

}

/* Galaxy S4 portrait and landscape */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) {

}

/* Galaxy S4 portrait */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: portrait) {

}

/* Galaxy S4 landscape */
@media screen 
  and (device-width: 320px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: landscape) {

}

/* Galaxy S5 portrait and landscape */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) {

}

/* Galaxy S5 portrait */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: portrait) {

}

/* Galaxy S5 landscape */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: landscape) {

}

/* HTC One portrait and landscape */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) {

}

/* HTC One portrait */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: portrait) {

}

/* HTC One landscape */
@media screen 
  and (device-width: 360px) 
  and (device-height: 640px) 
  and (-webkit-device-pixel-ratio: 3) 
  and (orientation: landscape) {

}

/*
  iPad 3 & 4 Media Queries
  If you're looking to target only 3rd and 4th generation Retina iPads 
  (or tablets with similar resolution) to add @2x graphics,
  or other features for the tablet's Retina display, use the following media queries.
*/

/* Retina iPad in portrait & landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px)
and (-webkit-min-device-pixel-ratio: 2) {
  
}

/* Retina iPad in landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : landscape)
and (-webkit-min-device-pixel-ratio: 2) {
  
}

/* Retina iPad in portrait */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : portrait)
and (-webkit-min-device-pixel-ratio: 2) {
  
}

/*
  iPad 1 & 2 Media Queries
  If you're looking to supply different graphics or choose different typography 
  for the lower resolution iPad display, the media queries below will work 
  like a charm in your responsive design!
*/

/* iPad 1 & 2 in portrait & landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (-webkit-min-device-pixel-ratio: 1) {
  
}

/* iPad 1 & 2 in landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : landscape)
and (-webkit-min-device-pixel-ratio: 1) {
  
}

/* iPad 1 & 2 in portrait */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : portrait) 
and (-webkit-min-device-pixel-ratio: 1) {
  
}

/* iPad mini in portrait & landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px)
and (-webkit-min-device-pixel-ratio: 1) {
  
}

/* iPad mini in landscape */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : landscape)
and (-webkit-min-device-pixel-ratio: 1) {

}

/* iPad mini in portrait */
@media only screen 
and (min-device-width : 768px) 
and (max-device-width : 1024px) 
and (orientation : portrait)
and (-webkit-min-device-pixel-ratio: 1) {
  
}

/* Galaxy Tab 10.1 portrait and landscape */
@media
  (min-device-width: 800px) 
  and (max-device-width: 1280px) {

}

/* Galaxy Tab 10.1 portrait */
@media 
  (max-device-width: 800px) 
  and (orientation: portrait) { 

}

/* Galaxy Tab 10.1 landscape */
@media 
  (max-device-width: 1280px) 
  and (orientation: landscape) { 

}

/* Asus Nexus 7 portrait and landscape */
@media screen 
  and (device-width: 601px) 
  and (device-height: 906px) 
  and (-webkit-min-device-pixel-ratio: 1.331) 
  and (-webkit-max-device-pixel-ratio: 1.332) {

}

/* Asus Nexus 7 portrait */
@media screen 
  and (device-width: 601px) 
  and (device-height: 906px) 
  and (-webkit-min-device-pixel-ratio: 1.331) 
  and (-webkit-max-device-pixel-ratio: 1.332) 
  and (orientation: portrait) {

}

/* Asus Nexus 7 landscape */
@media screen 
  and (device-width: 601px) 
  and (device-height: 906px) 
  and (-webkit-min-device-pixel-ratio: 1.331) 
  and (-webkit-max-device-pixel-ratio: 1.332) 
  and (orientation: landscape) {

}

/* Kindle Fire HD 7" portrait and landscape */
@media only screen 
  and (min-device-width: 800px) 
  and (max-device-width: 1280px) 
  and (-webkit-min-device-pixel-ratio: 1.5) {

}

/* Kindle Fire HD 7" portrait */
@media only screen 
  and (min-device-width: 800px) 
  and (max-device-width: 1280px) 
  and (-webkit-min-device-pixel-ratio: 1.5) 
  and (orientation: portrait) {
    
}

/* Kindle Fire HD 7" landscape */
@media only screen 
  and (min-device-width: 800px) 
  and (max-device-width: 1280px) 
  and (-webkit-min-device-pixel-ratio: 1.5) 
  and (orientation: landscape) {

}

/* Kindle Fire HD 8.9" portrait and landscape */
@media only screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 1.5) {

}

/* Kindle Fire HD 8.9" portrait */
@media only screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 1.5) 
  and (orientation: portrait) {
    
}

/* Kindle Fire HD 8.9" landscape */
@media only screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 1.5) 
  and (orientation: landscape) {

}

/* Laptops non-retina screens */
@media screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 1) {
    
}

/* Laptops retina screens */
@media screen 
  and (min-device-width: 1200px) 
  and (max-device-width: 1600px) 
  and (-webkit-min-device-pixel-ratio: 2)
  and (min-resolution: 192dpi) {
    
}

/* Apple Watch */
@media
  (max-device-width: 42mm)
  and (min-device-width: 38mm) { 

}

/* Moto 360 Watch */
@media 
  (max-device-width: 218px)
  and (max-device-height: 281px) { 

}
--------------------------------------------------------------------------------------------------------
[
	{
		"id": "0001",
		"type": "donut",
		"name": "Cake",
		"ppu": 0.55,
		"batters":
			{
				"batter":
					[
						{ "id": "1001", "type": "Regular" },
						{ "id": "1002", "type": "Chocolate" },
						{ "id": "1003", "type": "Blueberry" },
						{ "id": "1004", "type": "Devil's Food" }
					]
			},
		"topping":
			[
				{ "id": "5001", "type": "None" },
				{ "id": "5002", "type": "Glazed" },
				{ "id": "5005", "type": "Sugar" },
				{ "id": "5007", "type": "Powdered Sugar" },
				{ "id": "5006", "type": "Chocolate with Sprinkles" },
				{ "id": "5003", "type": "Chocolate" },
				{ "id": "5004", "type": "Maple" }
			]
	},
	{
		"id": "0002",
		"type": "donut",
		"name": "Raised",
		"ppu": 0.55,
		"batters":
			{
				"batter":
					[
						{ "id": "1001", "type": "Regular" }
					]
			},
		"topping":
			[
				{ "id": "5001", "type": "None" },
				{ "id": "5002", "type": "Glazed" },
				{ "id": "5005", "type": "Sugar" },
				{ "id": "5003", "type": "Chocolate" },
				{ "id": "5004", "type": "Maple" }
			]
	},
	{
		"id": "0003",
		"type": "donut",
		"name": "Old Fashioned",
		"ppu": 0.55,
		"batters":
			{
				"batter":
					[
						{ "id": "1001", "type": "Regular" },
						{ "id": "1002", "type": "Chocolate" }
					]
			},
		"topping":
			[
				{ "id": "5001", "type": "None" },
				{ "id": "5002", "type": "Glazed" },
				{ "id": "5003", "type": "Chocolate" },
				{ "id": "5004", "type": "Maple" }
			]
	}
]

{
	"id": "0001",
	"type": "donut",
	"name": "Cake",
	"image":
		{
			"url": "images/0001.jpg",
			"width": 200,
			"height": 200
		},
	"thumbnail":
		{
			"url": "images/thumbnails/0001.jpg",
			"width": 32,
			"height": 32
		}
}

------------------------------------------------------------------------------------------------------------------------
// obtain final and normal price
$finalPrice = $item->getProduct()->getFinalPrice();
$normalPrice = $item->getProduct()->getPrice();

// calculate original price of the item including tax
// first load the relevant product using object manager - Magento2
$item_id=$item->getProduct()->getId();
$om = \Magento\Framework\App\ObjectManager::getInstance();
$aproduct = $om->create('Magento\Catalog\Model\Product')->load($item_id);
// grab price info - original price and special price (if there is one)
$prices=$aproduct->getPriceInfo();
$original_price=$prices->getPrice('regular_price')->getAmount()->getValue();
$special_price=$prices->getPrice('special_price')->getAmount()->getValue();
$special_price=$prices->getPrice('')->getAmount()->getValue();
// display as necessary

------------------------------------------------------------------------------------------------------------------------
UPDATE `customer_entity`
SET `password_hash` = CONCAT(SHA2('xxxxxxxxYOURPASSWORD', 256), ':xxxxxxxx:1')
WHERE `entity_id` = 1;

------------------------------------------------------------------------------------------------------------------------

objectmanager

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$order = $objectManager->create('\Magento\Sales\Model\Order')
                           ->load($data['order_id']);



------------------------------------------------------------------------------------------------------------------------
Request set header
response JSON
namespace VendorName\ModuleName\Controller;
 
/**
 * Demo of authorization error for custom REST API
 */
class RestAuthorizationDemo extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $jsonResultFactory;
 
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
    }
 
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->jsonResultFactory->create();
        /** You may introduce your own constants for this custom REST API */
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN);
        $result->setData(['error_message' => __('What are you doing here?')]);
        return $result;
    }
}

To read data passed via get Method we simply use following two methods:

$this->getRequest()->getParams()
this will read all the get data but to read any specific data we use

$this->getRequest()->getParam('data');
To read data passed via POST method, we use following method:

$this->request->getPost()
this will read all the the data being passed via post. But if we want to read specific data then we will use

$this->getRequest()->getPost('data');

------------------------------------------------------------------------------------------------------------------------

  public function addOrderedQty($from = '', $to = '')
    {
        $connection = $this->getConnection();
        $orderTableAliasName = $connection->quoteIdentifier('order');

        $orderJoinCondition = [
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $connection->quoteInto("{$orderTableAliasName}.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];

        if ($from != '' && $to != '') {
            $fieldName = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->prepareBetweenSql($fieldName, $from, $to);
        }

        $this->getSelect()->reset()->from(
            ['order_items' => $this->getTable('sales_order_item')],
            ['ordered_qty' => 'SUM(order_items.qty_ordered)', 'order_items_name' => 'order_items.name']
        )->joinInner(
            ['order' => $this->getTable('sales_order')],
            implode(' AND ', $orderJoinCondition),
            []
        )->where(
            'parent_item_id IS NULL'
        )->group(
            'order_items.product_id'
        )->having(
            'SUM(order_items.qty_ordered) > ?',
            0
        );
        return $this;
    }


------------------------------------------------------------------------------------------------------------------------
<?php

namespace CodeTheatres\CustomApi\Api;
 
interface CustomRepositoryInterface
{
	/**
	 * Create custom Api.
	 *
	 * @param \CodeTheatres\CustomApi\Api\Data\CustomDataInterface $entity
	 * @return \CodeTheatres\CustomApi\Api\Data\CustomDataInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function create(
			\CodeTheatres\CustomApi\Api\Data\CustomDataInterface $entity
			);
	
	/**
	 * Update custom Api.
	 *
	 * @param \CodeTheatres\CustomApi\Api\Data\CustomDataInterface $entity
	 * @return \CodeTheatres\CustomApi\Api\Data\CustomDataInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function update(
			\CodeTheatres\CustomApi\Api\Data\CustomDataInterface $entity
			);
	
	/**
	 * Get custom Api.
	 *
	 * @param int $id
	 * @return \CodeTheatres\CustomApi\Api\Data\CustomDataInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function get($id
			);
	
	/**
	 * Delete custom Api.
	 *
	 * @param int $id
	 * @return bool Will returned True if deleted
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete($id
			);
}
?>

------------------------------------------------------------------------------------------------------------------------
Step 1: Configure Magento to use your S3 bucket

php bin/magento s3:config:set \
    --access-key-id=XXXXXXX \
    --secret-key=XXXXXXX \
    --bucket=XXXXXXX \
    --region=XXXXXXX
	
Step 2: Upload all pre-existing images to your S3 bucket

php bin/magento s3:storage:export

Step 3: Enable S3 integration for your Magento instance

php bin/magento s3:storage:enable


php bin/magento s3:config:set --access-key-id=AKIAIVRWAMTRWIZPQA5A --secret-key=R+YeqGns1HE5rrTFi2UYyeTd/92i7zzfGbROmgLM --bucket=s3migrationtestbucket --region=ap-south-1

access-key  : AKIAIVRWAMTRWIZPQA5A
secret-key=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
bucket : s3migrationtestbucket
region  : ap-south-1

------------------------------------------------------------------------------------------------------------------------

payment flow 

Here's the way I've always understood the concepts, and what you'll need to know to implement a payment module in Magento. Answers to your specific "where does this happen" are bolded below, although it's not quite as simple as you're hoping for.
Pre-internet, brick and mortar credit card transactions were a two stage process.
At the time of a sale, when the merchant took a consumer's credit card for a purchase they'd slide it through a point of sale device which would call into the credit card's central office and ask "is this card authorized for this network, and is this particular consumer's line of available credit large enough to allow this purchase". 

If the purchase was accepted (as opposed to declined), the charge was said to be authorized. The consumer would take their product, and the point of sale system/cash-register would note that the transaction was authorized. Then, at the end of a the day, or the end of the week, at some other predetermined regular schedule, or when the owner decided to stop drinking, the merchant would go though all their authorized receipts and send another request to the central office to capture the funds from the authorized transaction. Capturing the funds is what puts money in the merchant's account. 
This is still the model in use by most gateways, and is the domain model that Magento Inc. chose to implement for their payment modules. 
The way things are supposed to run is, when a consumer reaches the final checkout steps in a system like Magento, Magento issues an authorization request to the gateway's API. If the transaction is successful, the order is accepted into the system, and a unique ID from the authorization request is stored. Next, when the consumer's goods ship, a store owner uses the Magento admin to create an invoice. The creation of this invoice issues a capture request (using a store id returned from the authorization request). This is where these method calls are issued in Magento.

However, things get tricky because every payment gateway interprets these concepts a little differently, and every merchant interprets their "don't capture until we've shipped" responsibilities differently. In addition to the scenario described above, payment modules have a system configuration value known as a Payment Action. This can be set to Authorize Only, which will implement the flow described above. It can also be set to Authorize and Capture, which will both authorize and capture a payment when the order is placed. It gets even more confusing because although the method is called Authorize and Capture, current versions of Magento will only issue the capture request when set in this mode (at least for Authorize.net), and Authorize.net will, internally, leave capture requests in an authorized but not captured state for most of the day. How Magento handles orders and payments and invoices is one area of the codebase that changes a lot from version to version.

So, the idea behind the Magento payment module system is to shield you from the Cluster F--- that is programming payment Gateway logic. In your authorize method you implement a call to your payment gateway's authorize API (or perform whatever checks and logic you want to happen at this point). This method is passed a payment object and an amount. If you make you request/perform-your-logic and determine it's invalid for whatever reason, you throw an Exception with
Mage::throwException('...');

This tells Magento the authorization failed, and it will act accordingly (show an error message, etc.). Otherwise, you set data members on the Payment object and issue a 
return $this;
The data members are things you'll need later, when capturing the payment. Which brings us to the capture method of your Payment module. This method is also sent a payment object and an amount. In this method you issue your capture request. The payment object will have cc_trans_id data member 
$payment->getCcTransId()
which will allow you to issue a capture against your gateway. This is one of the data members you're responsible for saving up in authorize. Again, if your code determines the capture has failed, you throw an exception. Otherwise, you return $this.
The authorize.net payment module has good examples of how this is done.
app/code/core/Mage/Paygate/Model/Authorizenet.php
For example, consider this part of the capture method 
public function capture(Varien_Object $payment, $amount)
{
    if ($payment->getCcTransId()) {
        $payment->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
    } else {
        $payment->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
    }   

    $payment->setAmount($amount);
    $request= $this->_buildRequest($payment);
    $result = $this->_postRequest($request);
    //...
Here the capture method is checking if the payment has a cc_trans_id. Depending on the result, it sets anet_trans_type to either:
self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE
self::REQUEST_TYPE_AUTH_CAPTURE
This value is then used by the API request object to send an API call for either
Capturing a pre-authorized transaction
Immediate capture
------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------

------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------

v1-rest-url-how-you-can-explore-the-path-of-request



------------------------------------------------------------------------------------------------------------------------

