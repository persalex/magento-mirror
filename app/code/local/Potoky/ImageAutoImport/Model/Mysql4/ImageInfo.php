<?php

class Potoky_ImageAutoImport_Model_Mysql4_ImageInfo extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('imageautoimport/imageinfo', 'record_id');
    }
}
