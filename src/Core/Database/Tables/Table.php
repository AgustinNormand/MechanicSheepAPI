<?php

namespace API\Core\Database\Tables;

use XBase\Table as XbaseTable;
use API\Core\Database\Records\Record;
#use XBase\Record\VisualFoxproRecord;

class Table
{
    const CLIENTES = 'Clientes';
    const DETALLES = 'Detalles';
    const VEHICULOS = 'Vehiculos';
    const TRABAJOS = 'Trabajos';

    private $fullPath;

    private $table;

    private $recordCount;

    private $columns;

    public function __construct($fullPath, $databaseName)
    {
        $columnBuilder = "API\\Core\\Database\\ColumnBuilders\\ColumnBuilder{$databaseName}";
        $this->columns = $columnBuilder::$columns;
        $this->fullPath = $fullPath;
        $this->table = new XbaseTable($fullPath, $this->columns);
        $this->recordCount = $this->table->getRecordCount();
    }

    public function getUndeletedRecord($index)
    {
        $result = null;
        $xbaseRecord = $this->table->pickRecord($index);
        if(!$xbaseRecord->isDeleted())
            $result = $this->buildRecord($xbaseRecord);
        return $result;
    }

    public function getAllUndeletedRecords()
    {
        $result = [];
        for($i=0; $i < $this->recordCount; $i++){
            $xbaseRecord = $this->table->pickRecord($i);
            if(!$xbaseRecord->isDeleted())
                $result[] = $this->buildRecord($xbaseRecord);
        }
        return $result;
    }

    public function getUndeletedRecordsAfterIndex($index)
    {
        $result = [];
        for($i=$index; $i < $this->recordCount; $i++){
            $xbaseRecord = $this->table->pickRecord($i);
            if(!$xbaseRecord->isDeleted())
                $result[] = $this->buildRecord($xbaseRecord);
        }
        return $result;

    }

    public function getLastUndeletedRecord()
    {
        $result = null;
        $index = $this->recordCount - 1; #Obtengo el indice del último registro.
        while ($index >= 0 and $result == null)
        {
            $xbaseRecord = $this->table->pickRecord($index);
            if (!$xbaseRecord->isDeleted())
                $result = $this->buildRecord($xbaseRecord);
            $index--;
        }
        return $result;
    }

    #public function isDeleted($record){}

    #public function checkpoint()
    #{
    #    $fullPathOfBackupBase = $this->fullPath . ".bk";
    #    copy($this->fullPath, $fullPathOfBackupBase);
    #    return new Table($fullPathOfBackupBase, );
    #}

    public function isSameType($table)
    {
        return $this->table->getColumns() == $table->getColumns();
    }

    #public function getLastRecordIndex(){}

    #public function getLastUndeletedRecordIndex(){}

    public function close()
    {
        $this->table->close();
    }

    private function buildRecord($xbaseRecord)
    {
        $data = [];
        foreach($this->columns as $column){
            $key = array_search($column, $this->columns);
            $data[$key] = $xbaseRecord->$column;
        }
        return new Record($data);
    }

}