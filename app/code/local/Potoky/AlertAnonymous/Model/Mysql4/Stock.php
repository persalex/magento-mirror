<?php

class Potoky_AlertAnonymous_Model_Mysql4_Stock extends Mage_ProductAlert_Model_Mysql4_Stock
{
    protected function _construct()
    {
        $this->_init('alertanonymous/stock', 'alert_stock_id');
    }
}