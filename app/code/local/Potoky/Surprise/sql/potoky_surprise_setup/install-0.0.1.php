<?php

//die('want to setup');
/* @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$tabName = $installer->getTable('potoky_surprise/surprise');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('surprise_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned'  => true,
        'nullable' => false,
        'primary' => true
    ), 'Combination ID')
    ->addColumn('linked_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'=> false
    ), 'Surprise Product ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable' => false
    ), 'SurpriseD Product ID')
    ->addIndex(
    $installer->getIdxName(
        'potoky_surprise/surprise',
        array(
            'linked_product_id',
            'product_id',
        ),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array(
        'linked_product_id',
        'product_id',
    ),
    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);
$installer->getConnection()->createTable($table);

$installer->endSetup();

$data = array(
    array(
        'link_type_id'  => Potoky_Surprise_Model_Catalog_Product_Link::LINK_TYPE_SURPRISE,
        'code'          => 'surprise'
    )
);
foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('catalog/product_link_type'), $bind);
}

$data = array(
    array(
        'link_type_id'                  => Potoky_Surprise_Model_Catalog_Product_Link::LINK_TYPE_SURPRISE,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    )
);

$installer->getConnection()->insertMultiple($installer->getTable('catalog/product_link_attribute'), $data);