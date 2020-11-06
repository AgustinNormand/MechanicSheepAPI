<?php
    require '..' . DIRECTORY_SEPARATOR .'bootstrap.php';

    use API\Core\Database\Tables\Table;
    #use API\Core\Database\ColumnBuilders\ColumnBuilderClientes;

$clientes = new Table(__DIR__."/../../DBS_FOR_TESTS/DBS_UNTOUCHED/climae.dbf", Table::CLIENTES);

var_dump($clientes->getAllUndeletedRecords());