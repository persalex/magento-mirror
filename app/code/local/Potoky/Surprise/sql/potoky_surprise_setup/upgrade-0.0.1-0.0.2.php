<?php

/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$tabName = $installer->getTable('potoky_surprise/surprise');

$installer->getConnection()
    ->addColumn($tabName, 'quoted_qty', array(
        'nullable'  => true,
        'type'=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'precision' => 12,
        'scale' => 4,
        'default' => 0,
        'comment' => 'Quantity in cart'
    ));
$installer->getConnection()
    ->addColumn($tabName, 'sold_qty', array(
        'nulable' =>true,
        'type'=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'precision' => 12,
        'scale' => 4,
        'default' => 0,
        'comment' => 'Surprise sold quantity'
    ));
$installer->getConnection()
    ->addColumn($tabName, 'orders_qty', array(
        'nulable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'default' => 0,
        'comment' => 'Orders quantity'
    ));

$installer->endSetup();
