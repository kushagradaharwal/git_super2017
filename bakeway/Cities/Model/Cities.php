<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Cities
 * @author    Bakeway
 */

namespace Bakeway\Cities\Model;

use Magento\Framework\Model\AbstractModel;
use Bakeway\VendorNotification\Api\Data\SellerdevicedataInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Bakeway Cities Model.

 */
class Cities extends AbstractModel implements IdentityInterface
{
    /**
     * Bakeway Cities cache tag.
     */
    const CACHE_TAG = 'bakeway_cities';

    /**
     * @var string
     */
    protected $_cacheTag = 'bakeway_cities';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bakeway_cities';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Bakeway\Cities\Model\ResourceModel\Cities');
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSeller();
        }

        return parent::load($id, $field);
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }
}
