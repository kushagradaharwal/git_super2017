<?php

namespace Bakeway\EventsListing\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Cities.
     * @var \Bakeway\Cities\Model\CitiesFactory
     */

    protected $_cities;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Bakeway\Cities\Model\Cities $cities

    )
    {
        parent::__construct($context);
        $this->_cities = $cities;
    }


    public function getCityname($id)
    {
        $_citiesCollection = $this->_cities->getCollection()->addFieldToFilter("id", $id)
            ->getFirstItem();
        if (!empty($_citiesCollection)):
            return $_citiesCollection['name'];
        else:
            return '';
        endif;

    }


}
