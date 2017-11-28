<?php

namespace Bakeway\Shopapi\Api\Data;

interface ShopStatusInterface {

    const STATUS = 'status';

    /**
     * set Status
     * @param int
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     */
    public function setStatus($status);

    /**
     * get Status
     * @return \Bakeway\Shopapi\Api\Data\ShopStatusInterface
     */
    public function getStatus();
}
