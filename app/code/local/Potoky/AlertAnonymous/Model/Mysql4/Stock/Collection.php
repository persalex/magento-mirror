<?php

class Potoky_AlertAnonymous_Model_Mysql4_Stock_Collection extends Mage_ProductAlert_Model_Mysql4_Stock_Collection
{
    protected function _construct()
    {
        $this->_init('alertanonymous/stock');
    }
}