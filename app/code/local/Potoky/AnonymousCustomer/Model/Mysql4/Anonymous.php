<?php
/**
 * Created by PhpStorm.
 * User: light
 * Date: 7/7/2018
 * Time: 3:31 PM
 */

class Potoky_AnonymousCustomer_Model_Mysql4_Anonymous extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('anonymouscustomer/anonymous', 'anonymous_id');
    }
}