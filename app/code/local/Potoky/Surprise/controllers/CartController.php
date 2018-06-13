<?php

require_once(
    Mage::getModuleDir('controllers','Mage_Checkout').
    DS.'CartController.php');

class Potoky_Surprise_CartController extends Mage_Checkout_CartController
{
    public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            $cart->isSurpriseProduct($params['product'],
                ($params['qty'] == 0) ? 1 : $params['qty'],
                true);

            if($surproducts = $product->getSurpriseProducts()) {
                $nondecrease = (int) Mage::getStoreConfig('nondecrease/general/quantity');
                $surproducts = $cart->suggestSurpriseQty($surproducts, $params, $nondecrease);
                $this->pushSurprise($surproducts, $params['product'], $cart);
                $model = Mage::getModel('potoky_surprise/surprise');
                foreach ($surproducts as $key => $value) {
                    $update_id = $model->getCollection()
                        ->addFieldToFilter('product_id', array('eq' => $params['product']))
                        ->addFieldToFilter('linked_product_id', array('eq' => $key))
                        ->getAllIds()[0];
                        $quoted_qty = $model->load($update_id)['quoted_qty'];
                        $value = $value + $quoted_qty;
                        $model->setData(array('surprise_id' => $update_id, 'quoted_qty' => $value));
                        $model->save();
                }
            }

            if (!empty($related)) {
                $related = explode(',', $related);
                foreach ($related as $id) {
                    $relproduct = $this->_initProduct($id);
                    ($relproduct) ? $cart->addProduct($relproduct) : null;
                    if($surproducts = $relproduct->getSurpriseProducts()) {
                        $pars = ['product' => $id, 'qty' => 1];
                        $surproducts = $cart->suggestSurpriseQty($surproducts, $pars, $nondecrease);
                        $this->pushSurprise($surproducts, $pars['product'], $cart);
                        $model = Mage::getModel('potoky_surprise/surprise');
                        foreach ($surproducts as $key => $value) {
                            $update_id = $model->getCollection()
                                ->addFieldToFilter('product_id', array('eq' => $pars['product']))
                                ->addFieldToFilter('linked_product_id', array('eq' => $key))
                                ->getAllIds()[0];
                            $quoted_qty = $model->load($update_id)['quoted_qty'];
                            $value = $value + $quoted_qty;
                            $model->setData(array('surprise_id' => $update_id, 'quoted_qty' => $value));
                            $model->save();
                        }
                    }
                }
            }

            $cart->save();

            $this->cartFinalWorkout($cart);

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    protected function _initProduct($product_id = null)
    {
        $productId = (isset($product_id)) ? (int) $product_id : (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    public function pushSurprise($data = [], $parent_id, $cartin)
    {
        foreach ($data as $id => $qty) {
            if ($qty == 0) {
                continue;
            }
                $surproduct = $this->_initProduct($id);
                $surproduct->addCustomOption('option_random',$parent_id, $surproduct);

                $paramss = array('qty' => $qty);
                $cartin->addProduct($surproduct, $paramss);
        }
    }

    protected function _emptyShoppingCart()
    {
            $cart = $this->_getCart();
            Mage::register('cart', $cart);
            $carttree = $cart->getCarttree();
        try {
            $cart->truncate()->save();
            $this->_getSession()->setCartWasUpdated(true);
            Mage::dispatchEvent('cart_empty', array('info' => $carttree));
        } catch (Mage_Core_Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot update shopping cart.'));
        }
    }

    protected function _updateShoppingCart($data = null)
    {
        try {
            $cartData = ($data) ? $data : $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = (Mage::registry('cart')) ? Mage::registry('cart') : $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData);
                if($foradd = Mage::registry('foradd')) {
                    foreach ($foradd as $id => $qty) {
                        $product = $this->_initProduct($id);
                        if($surproducts = $product->getSurpriseProducts()) {
                            $pars = ['product' => $id, 'qty' => $qty];
                            $nondecrease = (int) Mage::getStoreConfig('nondecrease/general/quantity');
                            $surproducts = $cart->suggestSurpriseQty($surproducts, $pars, $nondecrease);
                            $this->pushSurprise($surproducts, $pars['product'], $cart);
                            $model = Mage::getModel('potoky_surprise/surprise');
                            foreach ($surproducts as $key => $value) {
                                $update_id = $model->getCollection()
                                    ->addFieldToFilter('product_id', array('eq' => $pars['product']))
                                    ->addFieldToFilter('linked_product_id', array('eq' => $key))
                                    ->getAllIds()[0];
                                $quoted_qty = $model->load($update_id)['quoted_qty'];
                                $value = $value + $quoted_qty;
                                $model->setData(array('surprise_id' => $update_id, 'quoted_qty' => $value));
                                $model->save();
                            }
                        }
                    }
                }
                $cart->save();
                $this->cartFinalWorkout($cart);
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update shopping cart.'));
            Mage::logException($e);
        }
    }

    protected function cartFinalWorkout($cart) {
        $quoteitems = $cart->getQuote()->getAllItems();
        $carttree = [];
        foreach ($quoteitems as $quoteitem) {
            $id = $quoteitem->getId();
            $filename = Mage::getBaseDir('media'). DS . 'surprise' . DS . $id.'.png';
            $options = $quoteitem->getOptionsByCode();
            $prod_id = $quoteitem->getProduct()->getId();
            if (!array_key_exists('option_random', $options)) {
                $carttree[$prod_id]['quoteitem'][$id] = $quoteitem->getQty();
            }
            else {
                $parent_id = $options['option_random']->getData()['value'];
                $carttree[$parent_id]['surprises'][$prod_id]['quoteitem'][$id] = $quoteitem->getQty();
            }
            if (array_key_exists('option_random', $options) && !file_exists($filename)) {
                $mainImage = Mage::getBaseDir('media') . DS . 'surprise' . DS . 'image.png';
                $watermarkImage = Mage::getBaseDir('media') . DS . 'surprise' . DS . 'watermark.png';
                $image = new Varien_Image($mainImage);
                $image->setWatermarkWidth(300);
                $image->setWatermarkHeigth(300);
                for ($i = 0; $i < 3; $i++) {
                    $color[] = rand(0, 255);
                }
                $image->backgroundColor($color);
                $image->watermark($watermarkImage, 400, 370, 100, false);
                $image->save($filename);
                $quoteitem->setOriginalCustomPrice(1);
                $quoteitem->save();
            }
        }
        $cart->setCarttree($carttree);
        foreach ($carttree as $branch) {
            $p = 0;
            foreach ($branch['quoteitem'] as $qty) {
                $p += $qty;
            }
            $s = 0;
            foreach ($branch['surprises'] as $sid) {
                foreach ($sid['quoteitem'] as $qty) {
                    $s += $qty;
                }
            }
            $temp = array_keys($branch['quoteitem']);
            foreach ($temp as $qii) {
                $item = null;
                foreach ($quoteitems as $quoteitem) {
                    if ($quoteitem->getId() == $qii) {
                        $item = $quoteitem;
                        break;
                    }
                }
                $price = $item->getProduct()->getPrice();
                $item->setOriginalCustomPrice($price - $s / $p);
                $item->save();
            }
        }
    }

    public function deleteAction()
    {
        if ($this->_validateFormKey()) {
            $id = (int)$this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $cart = $this->_getCart();
                    $carttree = $cart->getCarttree();
                    $prod_id = $this->_getQuote()
                        ->getItemById($id)
                        ->getProduct()
                        ->getId();
                    $check = $cart->isSurpriseItem($id);
                    if ($check) {
                        Mage::register('check', $check);
                        $cart->removeItem($id)->save();
                        $this->cartFinalWorkout($cart);
                    }
                    elseif ($carttree[$prod_id]['quoteitem'] && count($carttree[$prod_id]['quoteitem']) > 1) {
                        foreach ($carttree as $main_id) {
                            foreach ($main_id['quoteitem'] as $qii => $qty) {
                                $data[$qii] = array('qty' => ($qii == $id) ? 0 : $qty);
                            }
                        }
                        Mage::register('cart', $cart);
                        $this->_updateShoppingCart($data);
                    }
                    else {
                        Mage::register('prod_id', $prod_id);
                        $qty = -$carttree[$prod_id]['quoteitem'][$id];
                        $cart->isSurpriseProduct($prod_id, $qty, true);
                        $cart->removeItem($id)->save();
                        $this->cartFinalWorkout($cart);
                    }
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('Cannot remove the item.'));
        }

        $this->_redirectReferer(Mage::getUrl('*/*'));
    }

}

