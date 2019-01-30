<?php

class Potoky_AlertAnonymous_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Array of helper objects used in current module
     *
     * @var array
     */
    public static $helpers = [];

    /**
     * Array that shows helpers with their URIs used by each class
     * of the module that uses helpers
     *
     * @var array
     */
    private $classHelperMapping = [
        'Potoky_AlertAnonymous_AddController'         => [
            'allow'    => 'alertanonymous/allow',
            'entity'   => 'anonymouscustomer/entity',
            'registry' => 'alertanonymous/registry',
            'data'     => 'alertanonymous'
        ],
        'Potoky_AlertAnonymous_UnsubscribeController' => [
            'entity'   => 'anonymouscustomer/entity',
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Email_Template'  => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Customer'        => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Observer'        => [
            'registry' => 'alertanonymous/registry',
            'entity'   => 'anonymouscustomer/entity',
            'data'     => 'alertanonymous',
            'data_1'     => 'productalert',
        ],
        'Potoky_AlertAnonymous_Model_Price'           => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Model_Stock'           => [
            'registry' => 'alertanonymous/registry'
        ],
        'Potoky_AlertAnonymous_Block_Product_View'    => [
            'allow'    => 'alertanonymous/allow',
        ]
    ];

    /**
     * Create links for self::$helpers from the corresponding classes
     * passed as argument
     *
     * @var $classInstance
     */
    public function setUpHelpers($classInstance)
    {
        $className = get_class($classInstance);
        foreach ($this->classHelperMapping[$className] as $helperName => $rout) {
            if(!isset(self::$helpers[$helperName])) {
                self::$helpers[$helperName] = Mage::helper($rout);
            }
            $classInstance::$helpers[$helperName] = & self::$helpers[$helperName];
        }
    }
}