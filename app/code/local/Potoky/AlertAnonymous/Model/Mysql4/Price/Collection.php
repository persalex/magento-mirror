<?php

class Potoky_AlertAnonymous_Model_Mysql4_Price_Collection extends Mage_ProductAlert_Model_Mysql4_Price_Collection
{
    protected function _construct()
    {
        $this->_init('alertanonymous/price');
    }
}