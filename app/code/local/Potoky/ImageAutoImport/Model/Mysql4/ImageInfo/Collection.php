<?php

class Potoky_ImageAutoImport_Model_Mysql4_ImageInfo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    protected function _construct()
    {
        $this->_init('imageautoimport/imageinfo');
    }
}
