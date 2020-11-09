<?php

namespace API\Core\Database\Tables;

use XBase\Table as XbaseTable;
use API\Core\Database\Records\Record;
use API\Core\Config;
#use XBase\Record\VisualFoxproRecord;

class Table
{
    private $fullPath;

    private $table;

    private $recordCount;

    private $columns;

    public function __construct($fullPath, $databaseName)
    {
        $this->fullPath = $fullPath; 
        $columnBuilder = "API\\Core\\Enum\\DatabaseColumns\\DatabaseColumns{$databaseName}";
        $this->columns = $columnBuilder::$columns;
        $this->table = new XbaseTable($this->fullPath, $this->columns);
        $this->recordCount = $this->table->getRecordCount();
    }

    public function getRecord($index){
        $result = null;
        $xbaseRecord = $this->table->pickRecord($index);
        $result = $this->buildRecord($xbaseRecord);
        return $result;
    }

    public function getUndeletedRecord($index)
    {
        $result = null;
        $record = $this->getRecord($index);
        if(!$record->isDeleted())
            $result = $record;
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
        $index++; #Obtengo los registros que estén despues del indice dado.
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

    public function getLastUndeletedRecordIndex()
    {
        $result = null;
        $index = $this->recordCount - 1; #Obtengo el indice del último registro.
        while ($index >= 0 and $result == null)
        {
            $xbaseRecord = $this->table->pickRecord($index);
            if (!$xbaseRecord->isDeleted())
                $result = $index;
            $index--;
        }
        return $result;
    }

    public function getLastRecordIndex(){
        return $this->recordCount - 1;
    }

    public function isSameType($table)
    {
        return $this->table->getColumns() == $table->getColumns();
    }

    public function getColumns()
    {
        return $this->table->getColumns();
    }

    public function getUndeletedRecordCount(){}

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
        return new Record($xbaseRecord->getRecordIndex(), $data, $xbaseRecord->isDeleted());
    }

}