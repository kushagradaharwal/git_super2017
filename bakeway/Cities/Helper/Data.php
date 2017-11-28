<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Cities
 * @author    Bakeway
 */

namespace Bakeway\Cities\Helper;

use Bakeway\Cities\Model\ResourceModel\Cities\CollectionFactory as CitiesCollection;

/**
 * Bakeway Cities Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CitiesCollection
     */
    protected $cityData;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CitiesCollection $cityData
    )
    {
        $this->cityData = $cityData;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getCitiesOptionArray()
    {
        $cityCollection = $this->cityData->create()
            ->addFieldToFilter('is_active', 1);
        $options = [];
        $options[] = ['label' => __('---Please Select---'), 'value' => ''];

        foreach ($cityCollection as $city) {
            if ($city->getName() == 'Pune'):
                'selected=selected';
            endif;
            $options[] = ['label' => $city->getName(), 'value' => $city->getId()];
        }
        return $options;
    }

    /**
     * @param string $name
     * @return bool|int
     */
    public function getCityIdByName($name)
    {
        $cityid = $this->cityData->create()
            ->addFieldToFilter('name', ['like' => "%$name%"])
            ->getFirstItem()
            ->getId();

        if ($cityid) {
            return $cityid;
        } else {
            return false;
        }
    }

    public function getCityNameById($id)
    {
        $cityName = $this->cityData->create()
            ->addFieldToFilter('id', $id)
            ->getFirstItem()
            ->getName();

        return $cityName;
    }
}