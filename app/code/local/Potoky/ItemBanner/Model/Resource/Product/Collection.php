<?php

class Potoky_ItemBanner_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * The quantity of the banners prepared to be displayed on the page
     *
     * @var int
     */
    private $bannersQty = 0;

    /**
     * Set $this->bannersQty
     *
     * @param $qty
     * @return void
     */
    public function setBannersQty($qty)
    {
        $this->bannersQty = $qty;
    }

    /**
     * Get collection size adjusted with banners qty
     *
     * @return int
     */
    public function getSize()
    {
        return parent::getSize() + $this->bannersQty;
    }

    /**
     * Load entities records into items with addjustment of page size limit with banners
     *
     * @throws Exception
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        if(!$data = Mage::registry('potoky_itembanner')) {

            return parent::_loadEntities($printQuery, $logQuery);
        }

        $prevPageElementNumber = ($this->getCurPage() - 1) * $this->getPageSize();
        $prevPageProdNumber = $prevPageElementNumber - $data['previousPagesBannerQty'];

        if ($this->_pageSize) {
            $this->getSelect()->limit( $this->_pageSize, $prevPageProdNumber);
        }

        $this->printLogQuery($printQuery, $logQuery);

        try {
            /**
             * Prepare select query
             * @var string $query
             */
            $query = $this->_prepareSelect($this->getSelect());
            $rows = $this->_fetchAll($query);
        } catch (Exception $e) {
            Mage::printException($e, $query);
            $this->printLogQuery(true, true, $query);
            throw $e;
        }

        foreach ($rows as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            } else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }

        return $this;
    }
}