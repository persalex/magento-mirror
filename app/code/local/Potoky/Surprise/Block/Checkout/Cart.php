<?php


class Potoky_Surprise_Block_Checkout_Cart extends Mage_Checkout_Block_Cart
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getItems()
    {
        $cart = Mage::getSingleton('checkout/cart');
        $carttree = $cart->getCarttree();
        $sorted_items = [];
        if ($this->getCustomItems()) {
            $items = $this->getCustomItems();
        }
        else {
            $items = parent::getItems();
        }
        foreach ($carttree as $prod_id => $value) {
            $qiis = array_keys($value['quoteitem']);
            foreach ($qiis as $qii) {
                foreach ($items as $item) {
                    if ($qii == $item->getId()) {
                        $sorted_items[] = $item;
                    }
                }
            }
            $surps = $value['surprises'];
            foreach ($surps as $surp_id => $value) {
                $qiis = array_keys($value['quoteitem']);
                foreach ($qiis as $qii) {
                    foreach ($items as $item) {
                        if ($qii == $item->getId()) {
                            $sorted_items[] = $item;
                        }
                    }
                }
            }
        }

        return $sorted_items;
    }

}