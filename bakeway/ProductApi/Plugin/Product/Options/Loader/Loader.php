<?php

/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Bakeway\ProductApi\Plugin\Product\Options\Loader;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Api\Data\OptionInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Api\Data\OptionValueInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class Loader
 */
class Loader {

    /**
     * @var OptionValueInterfaceFactory
     */
    private $optionValueFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var attFactory
     */
    private $attFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_coreResource;

    /**
     * @param deliveryrangeHelper
     */
    protected $productapihelper;

    /**
     * ReadHandler constructor
     *
     * @param OptionValueInterfaceFactory $optionValueFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
    OptionValueInterfaceFactory $optionValueFactory, JoinProcessorInterface $extensionAttributesJoinProcessor, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attFactory, \Magento\Framework\App\ResourceConnection $coreResource
    , \Bakeway\ProductApi\Helper\Data $productapiHelper
    ) {
        $this->optionValueFactory = $optionValueFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->attFactory = $attFactory;
        $this->_coreResource = $coreResource;
        $this->productapihelper = $productapiHelper;
    }

    /**
     * @param ProductInterface $product
     * @return OptionInterface[]
     */
    public function aroundLoad(\Magento\ConfigurableProduct\Helper\Product\Options\Loader $subject, \Closure $proceed, ProductInterface $product) {
        $options = [];
        /** @var Configurable $typeInstance */
        //$proceed;
        $typeInstance = $product->getTypeInstance();
        $attributeCollection = $typeInstance->getConfigurableAttributeCollection($product);
        $this->extensionAttributesJoinProcessor->process($attributeCollection);

        $_skus = $this->productapihelper->getChildrenSkus($product);

        foreach ($attributeCollection as $attribute) {
            $values = [];
            $attributeOptions = $attribute->getOptions();
            if (is_array($attributeOptions)) {
                foreach ($attributeOptions as $option) {
                    /** @var \Magento\ConfigurableProduct\Api\Data\OptionValueInterface $value */
                    $value = $this->optionValueFactory->create();
                    $_Label = $this->getAttributeLabel($option['value_index']);
                    /*
                     * check avaiable skus
                     */
                    $_CheckAvaSku = $this->productapihelper->getOptionsSkus($option['value_index'], $_skus, $product);
                    $_array = array('value_index' => $option['value_index'], 'label' => $_Label, 'available_skus' =>
                        $_CheckAvaSku);
                    $values[] = $_array;
                }
            }
            $attribute->setValues($values);
            $options[] = $attribute;
        }
        return $options;
    }

    /**
     * @param $id
     * @return option label
     */
    public function getAttributeLabel($id) {
        $connection = $this->_coreResource->getConnection();
        $tableName = $connection->getTableName('eav_attribute_option_value'); //gives table name with prefix
        $sql = $connection->select()
                ->from($tableName, array('value'))
                ->where('option_id=' . $id);
        $result = $connection->fetchOne($sql);
        return $result;
    }

}
