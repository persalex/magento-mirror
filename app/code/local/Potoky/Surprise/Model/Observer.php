<?php


class Potoky_Surprise_Model_Observer
{
    public function orderWorkout(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $order->getQuote();
        $items = $quote->getItemsCollection()->getItems();
        $model = Mage::getModel('potoky_surprise/surprise');
        foreach ($items as $itm) {
            $itm_prod_id = $itm->getProduct()->getId();
            $options = $itm->getOptionsByCode();
            if (array_key_exists('option_random', $options)) {
                $parent_id = $options['option_random']->getData()['value'];
                $update_id = $model->getCollection()
                    ->addFieldToFilter('product_id', array('eq' => $parent_id))
                    ->addFieldToFilter('linked_product_id', array('eq' => $itm_prod_id))
                    ->getAllIds()[0];
                if ($update_id) {
                    $record = $model->load($update_id);
                    $quoted_qty = $record['quoted_qty'] - $itm->getData()['qty'];
                    $sold_qty = $record['sold_qty'] + $itm->getData()['qty'];
                    $orders_qty = $record['orders_qty'] + 1;
                    $model->setData(array(
                        'surprise_id' => $update_id,
                        'quoted_qty' => $quoted_qty,
                        'sold_qty' => $sold_qty,
                        'orders_qty' => $orders_qty
                    ));
                    $model->save();
                }
            }
        }
    }

    public function orderIsPayed(Varien_Event_Observer $observer) {
        $order = $observer->getInvoice()->getOrder();
        $items = $order->getAllItems();
        foreach ($items as $item) {
            $image_name = $item->getData('quote_item_id');
            $filename = Mage::getBaseDir('media'). DS . 'surprise' . DS . $image_name.'.png';
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    public function emptyCartWorkOut (Varien_Event_Observer $observer) {
        $cart = Mage::registry('cart');
        $carttree = $observer->getData('info');
        $model = Mage::getModel('potoky_surprise/surprise');
        foreach ($carttree as $par_id => $branch) {
            $parquant = 0;
            foreach ($branch['quoteitem'] as $qii => $quant) {
                $parquant -= $quant;
            }
            $cart->isSurpriseProduct($par_id, $parquant, true);
            if ($branch['surprises']) {
                foreach ($branch['surprises'] as $surp_id => $surprise) {
                    $del_qty = 0;
                    foreach ($surprise['quoteitem'] as $key => $value) {
                        $del_qty += $value;
                        unlink(Mage::getBaseDir('media'). DS . 'surprise' . DS . $key.'.png');
                    }
                    $update_id = $model->getCollection()
                        ->addFieldToFilter('product_id', array('eq' => $par_id))
                        ->addFieldToFilter('linked_product_id', array('eq' => $surp_id))
                        ->getAllIds()[0];
                    $record = $model->load($update_id);
                    $model->setData(array(
                        'surprise_id' => $update_id,
                        'quoted_qty'  => $record['quoted_qty'] - $del_qty
                    ));
                    $model->save();
                }
            }
        }
    }
}