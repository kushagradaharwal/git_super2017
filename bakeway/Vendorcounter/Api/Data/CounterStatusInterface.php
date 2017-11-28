<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Api\Data;

/**
 * Counter status data interface.
 */

interface CounterStatusInterface {

    const ID = 'id';
    const SKU = 'sku';
    const QTY = 'qty';
    const DATE = 'date';
    const SELLER_ID = 'seller_id';
    const OFFLINE_SALE = 'offline_sale';
    const ONLINE_SALE = 'online_sale';
    const AVAILABILTY = 'is_available';
    const TOTAL_COUNT = 'total_count';
    const LAST_UPDATED_TIME = 'last_updated_time';
    const LAST_UPDATED_BY = 'last_updated_by';
    const CARRY_FORWARDED_QTY = 'carry_forwarded_qty';

    /*     * #@- */

    /**
     * set $sku
     * @param string
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setSku($sku);

    /**
     * get $sku
     * @return string
     */
    public function getSku();

    /**
     * set $qty
     * @param int
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setQty($qty);

    /**
     * get $qty
     * @return int
     */
    public function getQty();

    /**
     * Get online sale
     * @return int
     */
    public function getOnlineSale();

    /**
     * Set online sale
     * @param int $qty
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setOnlineSale($qty);

    /**
     * Get offline sale
     * @return int
     */
    public function getOfflineSale();

    /**
     * Set offline sale
     * @param int $qty
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     */
    public function setOfflineSale($qty);
}
