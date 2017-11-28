<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcontract
 * @author    Bakeway
 */

namespace Bakeway\Vendorcontract\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Bakeway Vendorcontract Model.

 */
class Vendorcontract extends AbstractModel implements IdentityInterface
{
    /**
     * Bakeway Vendorcontract cache tag.
     */
    const CACHE_TAG = 'bakeway_vendorcontract';

    /**
     * @var string
     */
    protected $_cacheTag = 'bakeway_vendorcontract';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bakeway_vendorcontract';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Bakeway\Vendorcontract\Model\ResourceModel\Vendorcontract');
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
