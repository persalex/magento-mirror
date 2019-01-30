<?php

class Potoky_AlertAnonymous_Model_Mysql4_Price extends Mage_ProductAlert_Model_Mysql4_Price
{
    protected function _construct()
    {
        $this->_init('alertanonymous/price', 'alert_price_id');
    }
}
