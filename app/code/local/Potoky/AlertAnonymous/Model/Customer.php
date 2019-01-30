<?php

final class Potoky_AlertAnonymous_Model_Customer extends Mage_Customer_Model_Customer
{
    public static $helpers = [];

    function _construct()
    {
        if (empty(self::$helpers)) {
            Mage::helper('alertanonymous')->setUpHelpers($this);
        }

        if (self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $this->_init('anonymouscustomer/anonymous');
        } else {
            parent::_construct();
        }
    }

    /**
     * Swap the name of the customer ftom '' to Dear Customer
     * if it is an anonymous customer.
     *
     * @return string
     */
    public function getName()
    {
        $name = parent::getName();
        if(self::$helpers['registry']->getRegistry('parent_construct') === false) {
            $name = 'Dear Customer';
        }

        return $name;
    }
}