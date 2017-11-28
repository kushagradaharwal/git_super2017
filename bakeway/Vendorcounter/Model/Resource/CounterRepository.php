<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Model\Resource;

use Bakeway\Vendorcounter\Api\CounterRepositoryInterface;
use Webkul\Marketplace\Model\Seller as VendorShop;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\ResourceConnectionFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Bakeway\Vendorcounter\Model\CounterStatusFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;

class CounterRepository implements CounterRepositoryInterface {

    /**
     * @var ResourceConnectionFactory
     */
    protected $_resourceConnection;

    /**
     * @var VendorShop
     */
    protected $_vendorShop;

    /**
     * @var CounterStatusFactory
     */
    protected $_counterStatusFactory;

    /**
     * @var ProductModel
     */
    protected $_productModel;

    public function __construct(
        ResourceConnectionFactory $_resourceConnection,
        VendorShop $_vendorShop,
        CounterStatusFactory $_counterStatusFactory,
        ProductModel $productModel
    ) {
        $this->_resourceConnection = $_resourceConnection;
        $this->_vendorShop = $_vendorShop;
        $this->_counterStatusFactory = $_counterStatusFactory;
        $this->_productModel = $productModel;
    }

    /**
     * @param int $vendorId
     * @param \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[] $inventory
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function setVendorCounter($vendorId, array $inventory) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');

        $counterModel = $this->_counterStatusFactory->create();
        $counterStatusId = $this->getIdBySellerAndDate($vendorId, $current_date);
        if($counterStatusId !== false) {
            throw new AlreadyExistsException(__("Entry for the day already exist. Make different request to update the counter."));
        }

        $result = array();
        try{
            foreach($inventory as $data){
                $sku = $data->getSku();
                $qty = $data->getQty();
                $productId = $this->_productModel->getIdBySku($sku);
                if($productId === false){
                    throw new NotFoundException(__('The SKU "%1" provided is not exist in the catalog.',$sku));
                }else{
                    $counterModel = $this->_counterStatusFactory->create();
                    $counterModel->setSku($sku);
                    $counterModel->setQty($qty);
                    $counterModel->setData('seller_id', $vendorId);
                    $counterModel->setData('date', $current_date);
                    $counterModel->setData('last_updated_time', $date);
                    $counterModel->setData('last_updated_by', $vendorId);
                    $counterModel->setData('online_sale', 0);
                    $counterModel->setData('offline_sale', 0);

                    $counterModel->save();
                    $result[] = $counterModel;
                }
            }
        }catch (\Exception $exception){
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $result;
    }

     /**
     * @param int $vendorId
     * @param \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[] $inventory
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateVendorCounter($vendorId, array $inventory) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');

        $counterModel = $this->_counterStatusFactory->create();
        $counterStatusId = $this->getIdBySellerAndDate($vendorId, $current_date);
        if ($counterStatusId === false) {
            throw new NotFoundException(__('No entry has been found for the day. Please make a different request to add the data.'));
        }

        $skus = $this->getAllSkus($vendorId,$current_date);
        $result = array();
        $i=0;

        try {
            foreach ($inventory as $data) {
                $sku = $data->getSku();
                $qty = $data->getQty();
                if($qty < 0) {
                    $qty = 0;
                }
                $productId = $this->_productModel->getIdBySku($sku);
                if($productId === false){
                    throw new NotFoundException(__('The SKU "%1" provided is not exist in the catalog.',$sku));
                }else{
                    if (in_array($sku, $skus)) {
                        $id = $this->_counterStatusFactory->create()->getCounterId($sku, $vendorId, $current_date);
                        $counterModel = $counterModel->load($id);
                    }else{
                        $counterModel = $this->_counterStatusFactory->create();
                    }
                    $counterModel->setSku($sku);
                    $counterModel->setQty($qty);
                    $counterModel->setData('seller_id', $vendorId);
                    $counterModel->setData('date', $current_date);
                    $counterModel->setData('last_updated_time', $date);
                    $counterModel->setData('last_updated_by', $vendorId);

                    $counterModel->save();
                    $result[$i]['sku'] = $counterModel->getSku();
                    $result[$i]['qty'] = $counterModel->getQty();
                    $result[$i]['online_sale'] = $counterModel->getOnlineSale();
                    $result[$i]['offline_sale'] = $counterModel->getOfflineSale();
                    $i++;
                }
            }
        }catch (\Exception $exception){
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $result;
    }

    /**
     * @param int $vendorId
     * @param string $sku
     * @param bool $sold
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateCounterProduct($vendorId, $sku, $sold) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');

        $counterModel = $this->_counterStatusFactory->create();
        $id = $this->_counterStatusFactory->create()->getCounterId($sku, $vendorId, $current_date);

        if($id === false) {
            throw new NotFoundException(__('The SKU "%1" provided is not there on todays counter.',$sku));
        }

        try {
            $counterModel = $counterModel->load($id);

            $qty = $counterModel->getQty() - 1;
            if($qty < 0) {
                $qty = 0;
            }
            $offline_sale = $counterModel->getData('offline_sale');
            if ($sold === true && $qty > 0) {
                $counterModel->setSku($sku);
                $counterModel->setQty($qty);
                $counterModel->setData('online_sale', 0);
                $counterModel->setData('offline_sale', $offline_sale+1);
                $counterModel->setData('seller_id', $vendorId);
                $counterModel->setData('date', $current_date);
                $counterModel->setData('last_updated_time', $date);
                $counterModel->setData('last_updated_by', $vendorId);
            }
            $counterModel->save();
        }catch (\Exception $exception){
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $counterModel;
    }

    /**
     * Checking if the entry for the day is already exist
     * @param int $vendorId
     * @param datetime currentDate
     * @return int|false
     */
    public function getIdBySellerAndDate($vendorId, $currentDate)
    {
        $counterModel = $this->_counterStatusFactory->create();
        $counterStatus = $counterModel->getCollection()
                ->addFieldToFilter(
                    'seller_id', $vendorId
                )->addFieldToFilter(
                        'date', $currentDate
                )->addFieldToSelect(['id'])->load();
        $counterStatusId = $counterStatus->getFirstItem()->getId();
        if($counterStatusId){
            return $counterStatusId;
        }else{
            return false;
        }
    }

    /**
     * Get all the existing product's sku
     * @param int $vendorId
     * @param datetime currentDate
     * @return array
     */
    public function getAllSkus($vendorId, $currentDate)
    {
        $counterModel = $this->_counterStatusFactory->create();
        $counterCollection = $counterModel->getCollection()
                ->addFieldToFilter(
                    'seller_id', $vendorId
                )->addFieldToFilter(
                        'date', $currentDate
                )->addFieldToSelect(['sku']);

        $counterCollection->getSelect()->group('sku');

        $counterSkus = $counterCollection->getAllSkus();
        return $counterSkus;
    }

    /**
     * @param int $vendorId
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getRealtimeCounter($vendorId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        $date = $objDate->gmtDate();
        $current_date = date('Y/m/d');

        $counterModel = $this->_counterStatusFactory->create();
        $counterCollection = $counterModel->getCollection()
                ->addFieldToFilter('seller_id', $vendorId)
                ->addFieldToFilter('date', $current_date)
                ->addFieldToSelect(['sku','qty','online_sale','offline_sale'])
                ->removeFieldFromSelect('id');

        $counterItemsCount = $counterCollection->count();
        $result = $counterCollection->getData();

        if($counterItemsCount > 0){
            return $result;
        }else{
            $result = array();
            $lastUpdatedCounterDate = $counterModel->getCollection()
                ->addFieldToFilter('seller_id', $vendorId)
                ->addFieldToSelect(['date'])
                ->setOrder('date','desc')->getFirstItem()->getData('date');

            if($lastUpdatedCounterDate) {
                $remainingCounter = $counterModel->getCollection()
                ->addFieldToFilter('seller_id', $vendorId)
                ->addFieldToFilter('date', $lastUpdatedCounterDate)
                ->addFieldToFilter('qty', ['gt'=>0])
                ->addFieldToSelect(['id']);

                foreach($remainingCounter as $counter) {
                    $oldCounter = $counterModel->load($counter->getId());
                    $newCounter = $this->_counterStatusFactory->create();
                    $newCounter->setSku($oldCounter->getSku());
                    $newCounter->setQty($oldCounter->getQty());
                    $newCounter->setData('seller_id', $vendorId);
                    $newCounter->setData('online_sale', 0);
                    $newCounter->setData('offline_sale', 0);
                    $newCounter->setData('date', $current_date);
                    $newCounter->setData('last_updated_time', $date);
                    $newCounter->setData('last_updated_by', $vendorId);
                    $newCounter->setData('carry_forwarded_qty',$oldCounter->getQty());
                    $newCounter->save();
                    $result[] = $newCounter;
                }
            }
            return $result;
        }
    }
}
