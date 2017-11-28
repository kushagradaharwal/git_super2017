<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Model;

use Magento\Framework\Model\AbstractModel;
use Bakeway\Vendorcounter\Api\Data\CounterStatusInterface;

class CounterStatus extends AbstractModel implements CounterStatusInterface {

    protected function _construct()
    {
        $this->_init('Bakeway\Vendorcounter\Model\ResourceModel\CounterStatus');
    }

    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoute();
        }

        return parent::load($id, $field);
    }

    /**
     * set $sku
     * @param string
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setSku($sku) {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * get $sku
     * @return string
     */
    public function getSku() {
        $sku = parent::getData(self::SKU);
        return $sku;
    }

    /**
     * set $qty
     * @param int
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setQty($qty) {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * get $qty
     * @return int
     */
    public function getQty() {
        $qty = parent::getData(self::QTY);
        return $qty;
    }

    /**
     * Get counter identifier by sku vendorId and date
     *
     * @param string $sku
     * @param int $vendorId
     * @param datetime $currentDate
     * @return int
     */
    public function getCounterId($sku, $vendorId, $currentDate)
    {
        return $this->_getResource()->getCounterId($sku, $vendorId, $currentDate);
    }

    /**
     * Get online sale
     * @return int
     */
    public function getOnlineSale()
    {
        return parent::getData(self::ONLINE_SALE);
    }

    /**
     * Set online sale
     * @param int $qty
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setOnlineSale($qty)
    {
        return $this->setData(self::ONLINE_SALE, $qty);
    }

    /**
     * Get offline sale
     * @return int
     */
    public function getOfflineSale()
    {
        return parent::getData(self::OFFLINE_SALE);
    }

    /**
     * Set offline sale
     * @param int $qty
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setOfflineSale($qty)
    {
        return $this->setData(self::OFFLINE_SALE, $qty);
    }
}
