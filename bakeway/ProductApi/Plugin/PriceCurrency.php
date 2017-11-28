<?php


namespace Bakeway\ProductApi\plugin;

class PriceCurrency {

    public function aroundRound(Magento\Directory\Model\PriceCurrency $subject , \Closure $proceed ,$price)
    {

echo "file is overred";die;
 return $proceed;

    }

}
