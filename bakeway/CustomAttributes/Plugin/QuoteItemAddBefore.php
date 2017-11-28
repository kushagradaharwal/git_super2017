<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomAttributes
 * @author    Bakeway
 */

namespace Bakeway\CustomAttributes\Plugin;

use Bakeway\CustomAttributes\Helper\Data as CustomAttributeHelper;
use Magento\Framework\Exception\LocalizedException;

class QuoteItemAddBefore
{
    /**
     * @var CustomAttributeHelper
     */
    protected $helper;

    /**
     * QuoteItemAddBefore constructor.
     * @param CustomAttributeHelper $helper
     */
    public function __construct(
        CustomAttributeHelper $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param null $request
     * @param string $processMode
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        $subject,
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        $errors = $this->helper->addToCartCheck($product->getEntityId());
        
        if ($errors === true) {
            throw new LocalizedException(__('Could not add Product to Cart'));
        }
        return [$product, $request, $processMode];
    }
}