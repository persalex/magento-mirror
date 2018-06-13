<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('imageautoimport/imageinfo');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('record_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned'  => true,
        'nullable' => false,
        'primary' => true
    ), 'Record ID')
    ->addColumn('created_at',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'false',
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Time when the record was created, the image was queued')
    ->addColumn('loading_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'true',
        'default' => null
    ), 'Time when there was a trial to load the image, no matter successfull or not')
    ->addColumn('product_sku',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => 'false',
    ), 'Sku of the product the image is assigned to be attached to' )
    ->addColumn('image_url',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => 'false',
    ), 'Path to image')
    ->addColumn('image_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Image size compared to 1MB: lt => false, eq => null, gt => true')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false
    ), 'Status of image')
    ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true
    ), 'Error message if the image failed to get assigned to the product');
$installer->getConnection()->createTable($table);
$installer->endSetup();
