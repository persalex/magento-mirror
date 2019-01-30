<?php

class Potoky_AlertAnonymous_Helper_Registry extends Mage_Core_Helper_Abstract
{
    /**
     * Set Mage::registry for this module's key
     *
     * @param null $context
     * @param null $customerEntity
     * @param null $parentConstruct
     */
    public function setRegistry($context = null, $customerEntity = null, $parentConstruct = null)
    {
        Mage::unregister('potoky_alertanonymous');

        if ($context === null && $customerEntity === null && $parentConstruct === null) {
            return;
        }

        Mage::register('potoky_alertanonymous', array(
           'context'          => $context,
           'customer_entity'  => $customerEntity,
           'parent_construct' => $parentConstruct
        ));

    }

    /**
     * Get Mage::registry values for this module's key
     *
     * @param null $key
     * @return mixed|null
     */
    public function getRegistry($key = null)
    {
        $registry = Mage::registry('potoky_alertanonymous');

        if(!$registry || !$key) {
            return $registry;
        } else {
            switch ($key) {
                case 'context':
                    return $registry['context'];
                case 'customer_entity':
                    return $registry['customer_entity'];
                case 'parent_construct':
                    return $registry['parent_construct'];
                default:
                    return null;
            }
        }
    }
}