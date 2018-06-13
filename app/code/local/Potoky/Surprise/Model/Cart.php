<?php


class Potoky_Surprise_Model_Cart extends Mage_Checkout_Model_Cart
{
    protected $_carttree;

    public function suggestSurpriseQty($surprods, $params_arr, $nd_level = null)
    {
        $needed = ($params_arr['qty']) ? $params_arr['qty'] : '1';
        $model = Mage::getModel('potoky_surprise/surprise');
        $collection = $model->getCollection()
            ->setOrder('sold_qty', ($needed < 0) ? 'DESC' : 'ASC')
            ->addFieldToFilter('product_id', array('eq' => $params_arr['product']))->getData();
            $surprods_arr = [];
        foreach ($collection as $record) {
            foreach ($surprods as $surp) {
                if($surp->getId() == $record['linked_product_id']) {
                    $surprods_arr[] = $surp;
                    break;
                }
            }
        }
        $collection = ($needed < 0) ?
            $model->getCollection()
                ->setOrder('sold_qty', 'DESC')
                ->addFieldToFilter('product_id', array('eq' => $params_arr['product']))
                ->addFieldToFilter('quoted_qty', array('neq' => 0))->getData() :
            $model->getCollection()
                ->setOrder('sold_qty', ($needed < 0), 'ASC')
                ->addFieldToFilter('quoted_qty', array('neq' => 0))->getData();
        $surprevqty = [];
        if(count($collection) > 0) {
            foreach ($surprods_arr as $item) {
                $id = $item->getId();
                $surprevqty[$id] = 0;
                foreach ($collection as $record) {
                    if ($record['linked_product_id'] == $id) {
                        $surprevqty[$id] += $record['quoted_qty'];
                    }
                }
            }
        }
        else {
            foreach ($surprods_arr as $item) {
                $id = $item->getId();
                $surprevqty[$id] = 0;
            }
        }
        $surpsuggest = [];
        if ($needed < 0) {
            foreach ($surprods_arr as $item) {
                $surp_id = $item->getData()['entity_id'];
                $allowed = -$surprevqty[$surp_id];
                $qty = ($needed > $allowed) ? $needed : $allowed;
                $needed = $needed - $qty;
                $surpsuggest[$surp_id] = $qty;
                if ($needed == 0) {
                    break;
                }
            }
        }
        else {
            foreach ($surprods_arr as $item) {
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item);
                $stock_qty = $stock->getQty();
                $surp_as_comm = $model
                    ->getCollection()
                    ->addFieldToFilter('surp_as_comm', array('neq' => 0))
                    ->addFieldToFilter('linked_product_id', array('eq' => $item->getData('entity_id')))
                    ->getData()[0]['surp_as_comm'];
                if ($stock_qty > $nd_level) {
                    $surp_id = $item->getData()['entity_id'];
                    $allowed = $stock_qty - $surprevqty[$surp_id] - $nd_level - $surp_as_comm;
                    if ($allowed <= 0) {
                        continue;
                    }
                    $qty = ($needed < $allowed) ? $needed : $allowed;
                    $needed = $needed - $qty;
                    $surpsuggest[$surp_id] = $qty;
                    if ($needed == 0) {
                        break;
                    }
                }
            }
        }
        return $surpsuggest;
    }

    public function setCarttree($carttree = []) {
        $this->_carttree = $carttree;
        return $this;
    }

    public function getCarttree() {

        if (isset($this->_carttree)) {
            return $this->_carttree;
        }
        $carttree = [];
        $quoteitems = $this->getQuote()->getAllItems();
        foreach ($quoteitems as $quoteitem) {
            $id = $quoteitem->getId();
            $options = $quoteitem->getOptionsByCode();
            $prod_id = $quoteitem->getProduct()->getId();
            if (!array_key_exists('option_random', $options)) {
                $carttree[$prod_id]['quoteitem'][$id] = $quoteitem->getQty();
            } else {
                $parent_id = $options['option_random']->getData()['value'];
                $carttree[$parent_id]['surprises'][$prod_id]['quoteitem'][$id] = $quoteitem->getQty();
            }
        }
        $this->setCarttree($carttree);
        return $this->_carttree;
    }

    public function suggestItemsQty($data)
    {
        $carttree = $this->getCarttree();
        $update_diffs = [];
        foreach ($data as $itemId => $itemInfo) {
            if (!isset($itemInfo['qty'])) {
                continue;
            }

            $qty = (float)$itemInfo['qty'];
            $quoteItem = $this->getQuote()->getItemById($itemId);
            $product = $quoteItem->getProduct();

            if ($qty <= 0) {
                $update_diffs[$product->getId()]['quoteitem'][$itemId] =
                    (float) $itemInfo['qty'] - (float) $carttree[$product->getId()]['quoteitem'][$itemId];
                continue;
            }

            if (!$quoteItem) {
                continue;
            }

            if (!$product) {
                continue;
            }

            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = $product->getStockItem();
            if (!$stockItem) {
                continue;
            }

            $update_diffs[$product->getId()]['quoteitem'][$itemId] =
                (float) $itemInfo['qty'] - (float) $carttree[$product->getId()]['quoteitem'][$itemId];

            $data[$itemId]['before_suggest_qty'] = $qty;
            $data[$itemId]['qty'] = $stockItem->suggestQty($qty);
        }
        $foradd = [];
        foreach ($update_diffs as $prod_id => $attr) {
            $diff = (float) 0;
            foreach ($attr['quoteitem'] as $value) {
                $diff += (float) $value;
            }
            if ($diff == 0) {
                continue;
            }
            $this->isSurpriseProduct($prod_id, $diff, true);
            $prodsum = 0;
            $surpsum = 0;
            foreach ($carttree[$prod_id]['quoteitem'] as $qii => $quant) {
                $prodsum += $quant;
            }
            foreach ($carttree[$prod_id]['surprises'] as $surp_id) {
                foreach ($surp_id['quoteitem'] as $qii => $quant) {
                    $surpsum += $quant;
                }
            }
            if ($diff > 0) {
                $diff = $diff + $prodsum - $surpsum;
                $foradd[$prod_id] = $diff;
                continue;
            }
            $diff = $diff + $prodsum - $surpsum;
            if ($diff > 0) {
                continue;
            }
            $prod = $this->_getProduct($prod_id);
            if ($diff < 0 && $surproducts = $prod->getSurpriseProducts()) {
                $model = Mage::getModel('potoky_surprise/surprise');
                $params = ['product' => $prod_id, 'qty' => $diff];
                $surprods = $this->suggestSurpriseQty($surproducts, $params);
                foreach ($surprods as $surp_id => $qty) {
                    $update_id = $model->getCollection()
                        ->addFieldToFilter('product_id', array('eq' =>$params['product']))
                        ->addFieldToFilter('linked_product_id', array('eq' =>$surp_id))
                        ->getAllIds()[0];
                    $quoted_qty = $model->load($update_id)['quoted_qty'] + $qty;
                    if ($quoted_qty == 0) {
                        $surp_qii = array_keys($carttree[$prod_id]['surprises'][$surp_id]['quoteitem']);
                        $surp_qii = $surp_qii[0];
                        $filename = Mage::getBaseDir('media'). DS . 'surprise' . DS . $surp_qii.'.png';
                        if (file_exists($filename)) {
                            unlink($filename);
                        }
                    }
                    $model->setData(array(
                       'surprise_id' => $update_id,
                       'quoted_qty'  => $quoted_qty
                    ));
                    $model->save();
                    foreach ($carttree[$prod_id]['surprises'][$surp_id]['quoteitem'] as $itmId => $amount) {
                        $data[$itmId]['before_suggest_qty'] = $amount + $qty;
                        $data[$itmId]['qty'] = $stockItem->suggestQty(($amount + $qty));
                    }
                }
            }
        }
        if (count($foradd) > 0) {
            Mage::register('foradd', $foradd);
        }
        return $data;
    }

    public function removeItem($itemId)
    {
        $carttree = $this->getCarttree();
        $quote = $this->getQuote();
        $prod_id = Mage::registry('prod_id');
        if ($surprises = $carttree[$prod_id]['surprises']) {
            $model = Mage::getModel('potoky_surprise/surprise');
            foreach ($surprises as $key => $value ) {
                $qiis = array_keys($value['quoteitem']);
                $qii = array_values($qiis)[0];
                $quote->removeItem($qii);
                $filename = Mage::getBaseDir('media'). DS . 'surprise' . DS . $qii.'.png';
                $update_id = $model->getCollection()
                    ->addFieldToFilter('product_id', array('eq' =>$prod_id))
                    ->addFieldToFilter('linked_product_id', array('eq' => $key))
                    ->getAllIds()[0];
                $quoted_qty = $model->load($update_id)['quoted_qty'] - $value['quoteitem'][$qii];
                if (file_exists($filename)) {
                    unlink($filename);
                }
                $model->setData(array(
                    'surprise_id' => $update_id,
                    'quoted_qty'  => $quoted_qty
                ));
                $model->save();
            }
        }
        $ids = Mage::registry('check');
        if ($ids) {
            $item =$quote->getItemById($itemId);
            $model = Mage::getModel('potoky_surprise/surprise');
            $filename = Mage::getBaseDir('media'). DS . 'surprise' . DS . $itemId.'.png';
            $update_id = $model->getCollection()
                ->addFieldToFilter('product_id', array('eq' =>$ids['main']))
                ->addFieldToFilter('linked_product_id', array('eq' => $ids['surprise']))
                ->getAllIds()[0];
            $quoted_qty = $model->load($update_id)['quoted_qty'] - $item->getQty();
                $model->setData(array(
                'surprise_id' => $update_id,
                'quoted_qty'  => $quoted_qty
            ));
            $model->save();
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
        $quote->removeItem($itemId);
        return $this;
    }

    public function isSurpriseItem($quote_item_id) {
        $carttree = $this->getCarttree();
        foreach ($carttree as $prod_id => $value1) {
            foreach ($value1['surprises'] as $surp_id => $value2){
                if (array_keys($value2['quoteitem'])[0] == $quote_item_id) {
                    $info['main'] = $prod_id;
                    $info{'surprise'} = $surp_id;
                    return $info;
                }
            }
        }
        return false;
    }

    public function isSurpriseProduct($prod_id, $qty, $exec = false) {
        $model = Mage::getModel('potoky_surprise/surprise');
        $first_rec = $model->getCollection()
            ->addFieldToFilter('linked_product_id', array('eq' => $prod_id))
            ->setOrder('surprise_id', 'ASC')
            ->getData()[0];
        $is = false;
        if (count($first_rec) > 0) {
            $model->setData(array(
                'surprise_id' => $first_rec['surprise_id'],
                'surp_as_comm'  => $first_rec['surp_as_comm'] + $qty
            ));
            $is = true;
        }
        if ($exec && $is) {
            $model->save();
        }
        return $is;
    }

}