<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PartnerWebapi
 * @author    Bakeway
 */

namespace Bakeway\PartnerWebapi\Api;

interface SearchPartnerInterface
{
    /**
     * Get Partner List.
     *
     * @api
     * @param string|null $city
     * @param string|null $lat
     * @param string|null $long
     * @param string|null $deliverydate
     * @param string|null $searchterm
     * @param \Magento\Framework\Api\SearchCriteria|null $searchCriteria The search criteria.
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getSearchList($city = null, $lat = null, $long = null, $deliverydate = null, $searchterm = null, \Magento\Framework\Api\SearchCriteria $searchCriteria = null);

    /**
     * Get Autocomplete List.
     *
     * @api
     * @param string|null $city
     * @param string|null $lat
     * @param string|null $long
     * @param string|null $deliverydate
     * @param string|null $searchterm
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getSearchAutoCompleteList($city = null, $lat = null, $long = null, $deliverydate = null, $searchterm = null);

    /**
     * Get Partner Details.
     * @param int $vendorId
     * @param string|null $store
     * @return array
     */
    public function getPartnerDetails($vendorId, $store = null);

    /**
     * Get Partner Filters
     * @return array
     */
    public function getPartnerFilters();

    /**
     * Get Product Filters
     * @return array
     */
    public function getProductFilters();
}