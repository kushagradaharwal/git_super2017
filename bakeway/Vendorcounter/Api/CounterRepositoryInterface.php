<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Api;

/**
 * Counter repository interface.
 */
interface CounterRepositoryInterface {

    /**
     * @param int $vendorId
     * @param \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[] $inventory
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function setVendorCounter($vendorId, array $inventory);

    /**
     * @param int $vendorId
     * @param \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[] $inventory
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateVendorCounter($vendorId, array $inventory);

    /**
     * @param int $vendorId
     * @param string $sku
     * @param bool $sold
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function updateCounterProduct($vendorId, $sku, $sold);

    /**
     * @param int $vendorId
     * @return \Bakeway\Vendorcounter\Api\Data\CounterStatusInterface[]
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getRealtimeCounter($vendorId);
}
