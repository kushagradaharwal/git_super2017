<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomerWebapi
 * @author    Bakeway
 */

namespace Bakeway\CustomerWebapi\Api;

interface CustomerAccountRepositoryInterface
{
    /**
     * @api
     * @param mixed $data
     * @return mixed
     */
    public function socialLogin($data);
}