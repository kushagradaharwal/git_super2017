<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_ProductApi
 * @author    Bakeway
 */
namespace Bakeway\ProductApi\Api;

/**
 * Catalog SEO Interface.
 */
interface CatalogSeoRepositoryInterface {
    
    /**
     * Get Url Details
     * @param string $url
     * @param string|null $store
     * @return array
     * @throws NotFoundException
     */
    public function getUrlDetails($url, $store = null);
}