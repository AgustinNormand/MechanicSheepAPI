<?php

namespace API\Core\Comparators;

use XBase\Table;
use \Exception;

use API\Core\Log;

class ComparatorBase
{

    private $oldTable = null;
    private $newTable = null;

    private $leftSearchRange = null;
    private $rightSearchRange = null;

    private $columns;

    private $lastUndeletedRecordIndexNewTable = null;
    private $lastUndeletedRecordIndexOldTable = null;

    protected $newRecordsFound = [];
    protected $deletedRecordsFound = [];
    protected $modifiedRecordsFound = [];

    private $dbfFilePath = null;
    
    function setCheckpoint($dbfFilePath){
        $this->dbfFilePath = $dbfFilePath;
        copy($dbfFilePath, $dbfFilePath.'.bk');
    }


    function checkDiferences($leftSearchRange, $rightSearchRange){
        $this->oldTable = new Table($this->dbfFilePath.'.bk', null, 'CP1252');
        $this->newTable = new Table($this->dbfFilePath, null, 'CP1252');

        $this->columns = $this->oldTable->getColumns();
        $newColumns = $this->newTable->getColumns();

        if ($newColumns != $this->columns){
            Log::error("Las columnas de las tablas a comparar son diferentes: {$this->newTable} , {$newColumns} | {$this->oldTable} , {$this->columns}");
            throw new Exception('Columns of tables are different.');
        }

        $this->lastUndeletedRecordIndexNewTable = $this->getLastUndeletedRecordIndex($this->newTable);
        if ($this->lastUndeletedRecordIndexNewTable == null){
            Log::error("La tabla nueva, no tiene ningun registro que no esé eliminado: {$this->newTable}");
            throw new Exception('Cannot found an undeleted record in NewTable');
        }

        $this->lastUndeletedRecordIndexOldTable = $this->getLastUndeletedRecordIndex($this->oldTable);
            if ($this->lastUndeletedRecordIndexOldTable == null){
                Log::error("La tabla vieja, no tiene ningun registro que no esé eliminado: {$this->oldTable}");
                throw new Exception('Cannot found an undeleted record in OldTable');
            }
    
        $this->leftSearchRange = $leftSearchRange;
        $this->rightSearchRange = $rightSearchRange;

        Log::debug("Verificando registros nuevos.");
        $this->checkNewRecords();
     
        Log::debug("Verificando registros eliminados.");
        $this->checkDeletedRecords();

        Log::debug("Verificando registros modificados.");
        $this->checkModifyRecords();

        $this->setCheckpoint($this->dbfFilePath);

        $this->closeDatabases();
    }

    function checkNewRecords(){
        $j = $this->oldTable->getRecordCount();

        $limiteInferior = $this->newTable->getRecordCount() - 1;

        while($j <= $limiteInferior)
        {
            $recordNewTable = $this->newTable->pickRecord($j);
            if(!$recordNewTable->isDeleted())
                $this->addNewRecord($recordNewTable);
            $j++;
        }
    }

    function checkDeletedRecords()
    {
        $i = $this->oldTable->getRecordCount() - 1;

        $oldRecordsAnalized = 0;

        while($i>=0 and (($oldRecordsAnalized <= $this->leftSearchRange) or $this->leftSearchRange == null))
        {
            $recordOldTable = $this->oldTable->pickRecord($i);
            if(!$recordOldTable->isDeleted())
            {
                $oldRecordsAnalized++;
                $recordNewTable = $this->newTable->pickRecord($i);
                if($recordNewTable->isDeleted())
                    $this->addDeletedRecord($recordNewTable);
            }
            $i--;
        }
    }

    function checkModifyRecords()
    {
        $i = $this->lastUndeletedRecordIndexOldTable;

        $recordsAnalized = 0;

        while($i >= 0 and ($recordsAnalized < $this->leftSearchRange or $this->leftSearchRange == null)) #Mientras el indice sea valido y no haya analizado tantos registros como el searchRange
        {
            $recordOldTable = $this->oldTable->pickRecord($i);
            
            if(!$recordOldTable->isDeleted()){  
                $recordsAnalized++; #Descuento registros por analizar del serachRange
                
                $recordNewTable = $this->newTable->pickRecord($i);
                if (!$recordNewTable->isDeleted() and !$this->equals($recordOldTable, $recordNewTable))
                    $this->addModifiedRecord($recordOldTable, $recordNewTable);
            }
            $i--;
        }

    }
    
    function getLastUndeletedRecordIndex($table){
        $lastUndeletedRecordIndex = null;
        $index = $table->getRecordCount() - 1;

        while ($lastUndeletedRecordIndex == null and $index >= 0){
            $record = $table->pickRecord($index);
            if (!$record->isDeleted()){
                $lastUndeletedRecordIndex = $index;
            } else{
                $index--;
            }
        }
        return $lastUndeletedRecordIndex;
    }

    function equals($recordOne, $recordTwo){
        $result = true;
        foreach($this->columns as $column){
            if($recordOne->$column != $recordTwo->$column){
                $result = false;
                break;
            }
        }
        return $result;
    }

    function exists($recordToSearch, $array){
        $result = false;
        if($array == $this->modifiedRecordsFound)
        {
            foreach($array as $record)
                if ($this->equals($recordToSearch, $record['from'])){
                    $result = true;
                break;
                }
        }
        else
            foreach($array as $record)
                if ($this->equals($recordToSearch, $record)){
                    $result = true;
                break;
                }

        return $result;

    }

    function closeDatabases(){
        $this->newTable->close();
        $this->oldTable->close();
    }

    function getAcumulatedNewRecordsFound(){
        return $this->newRecordsFound;
    }

    function getAcumulatedDeletedRecordsFound(){
        return $this->deletedRecordsFound;
    }

    function getAcumulatedModifiedRecordsFound(){
        return $this->modifiedRecordsFound;
    }

    function addModifiedRecord($from, $to){
        if(!$this->exists($from, $this->modifiedRecordsFound)){
            $index = count($this->modifiedRecordsFound);
            $this->modifiedRecordsFound[$index]["from"] = $from;
            $this->modifiedRecordsFound[$index]["to"] = $to;
            Log::info("Modified from: ", [$this->toString($from)]);
            Log::info("Modified to: ", [$this->toString($to)]);
        }
    }

    function addNewRecord($record){
        if (!$this->exists($record, $this->newRecordsFound)){
            $this->newRecordsFound[] = $record;
            Log::info("New: ", [$this->toString($record)]);
        }
    }

    function addDeletedRecord($record){
        if (!$this->exists($record, $this->deletedRecordsFound)){
            $this->deletedRecordsFound[] = $record;
            Log::info("Deleted: ", [$this->toString($record)]);
        }
    }

    function resetAcumulatedRecords() ##Esto hay que hacerlo mucho mejor
    {
        $this->newRecordsFound = [];
        $this->deletedRecordsFound = [];
        $this->modifiedRecordsFound = [];
    }

    function toString($record)
    {
        $recordStr = '';
        foreach($this->columns as $column)
            $recordStr = $recordStr . $record->$column." ";
        #$recordStr = str_replace(chr(165), "Ñ", $recordStr); //Parchea las Ñ mayuscula
        #$recordStr = str_replace(chr(164), "ñ", $recordStr); //Parchea las Ñ minúscula
        return $recordStr;
    }
}
?>