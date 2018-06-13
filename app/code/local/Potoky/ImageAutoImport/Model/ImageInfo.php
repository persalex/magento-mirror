<?php

class Potoky_ImageAutoImport_Model_ImageInfo extends Mage_Core_Model_Abstract
{
    private $_adapter = null;

    private $_rows = [];

    protected function _construct()
    {
        $this->_init('imageautoimport/imageinfo');
    }

    private function _setRows()
    {
        while ($this->_adapter->valid()) {
            $key = $this->_adapter->key();
            $row = $this->_adapter->current();
            $this->_rows[$key] = $row;
            $this->_adapter->next();
        }
    }

    public function isSetAdapter() {
        if ($this->_adapter === null) {
            throw new Exception('Adapter is not set. Please contact Your developer to resolve the issue', 0);
        }
        return true;
    }

    public function setAdapter($sourceFile)
    {
        $this->_rows = [];
        $this->_adapter = Mage_ImportExport_Model_Import_Adapter::findAdapterFor($sourceFile);
        $this->_setRows();
        return $this;
    }

    public function getRows() {
        return $this->_rows;
    }

    public function validate()
    {
        $this->isSetAdapter();
        if (empty($this->_rows)) {
            throw new Exception(Mage::HELPER('imageautoimport')->__("File columns do not contain any information."));
        }

        $colNames = $this->_adapter->getColNames();
        $required = ['sku', 'url'];
        $errorMessage = '';
        foreach ($required as $value) {
            if (!in_array($value, $colNames)) {
                $errorMessage .= sprintf(
                    "%s\"%s\"" . Mage::HELPER('imageautoimport')->__(" is a required column.") . "%s",
                    '<p>',
                    $value,
                    '</p>'
                );
            }
        }
        foreach ($colNames as $colName) {
            if (!in_array($colName, $required)) {
                $errorMessage .= sprintf(
                    "\"%s\"" . Mage::HELPER('imageautoimport')->__(" is not a valid column name.") . "%s",
                    $colName,
                    '</br>'
                );
            }
        }
        if (!empty($errorMessage)) {
            throw new Exception(
                sprintf(
                    "%s" . Mage::HELPER('imageautoimport')->__("Column name error(s):") . '%s%s%s',
                    '<pre>',
                    '</br></br>',
                    $errorMessage,
                    '</pre>'
                ),
                 1
            );
        }

        $product = Mage::getModel('catalog/product');
        $errorMessage = '';
        foreach ($this->_rows as $key => $row) {
            $rowErrorMessage = '';
            $hasBeen = sprintf(
                "%s" . Mage::HELPER('imageautoimport')->__("You have errors(s) in row %s:%s"),
                '<pre><p>',
                $key + 1,
                '</br>'
            );
            if (!$product->loadByAttribute('sku', $row['sku'])) {
                $errorMessage .= sprintf(
                    "%s" . Mage::HELPER('imageautoimport')->__("Product with sku \"%s\" doesn't exist.%s"),
                    $hasBeen,
                    $row['sku'],
                    '</br>'
                );
                $hasBeen = '';
            }
            if (!filter_var($row['url'], FILTER_VALIDATE_URL)) {
                $errorMessage .= sprintf(
                    "%s" . Mage::HELPER('imageautoimport')->__("\"%s\" is not a valid URL.%s"),
                    $hasBeen,
                    $row['url'],
                    '</br>'
                );
            }
            $errorMessage .= ($rowErrorMessage != '') ? $rowErrorMessage. '</p></pre>' : '';
        }
        if (!empty($errorMessage)) {
            throw new Exception($errorMessage, 2);
        }
        return $this;
    }
}
