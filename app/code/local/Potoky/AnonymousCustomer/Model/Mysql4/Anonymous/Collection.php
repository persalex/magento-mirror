<?php
/**
 * Created by PhpStorm.
 * User: light
 * Date: 7/7/2018
 * Time: 3:39 PM
 */

class Potoky_AnonymousCustomer_Model_Mysql4_Anonymous_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('anonymouscustomer/anonymous');
    }
}