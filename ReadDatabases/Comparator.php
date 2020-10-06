<?php

require "./vendor/autoload.php";
require_once('Database.php');
use XBase\Table;

Class Comparator
{

    var $oldTable = null;
    var $newTable = null;

    private $searchRange = 150;

    private $columns;

    private $lastUndeletedRecordIndexNewTable = null;
    private $lastUndeletedRecordIndexOldTable = null;

    private $newRecordsFound = array();
    private $deletedRecordsFound = array();
    
    function __construct(){
    }

    function setCheckpoint($oldDbfFilePath){
        copy($oldDbfFilePath, $oldDbfFilePath.'.bk');
        $this->oldTable = new Table($oldDbfFilePath.'.bk');
        $this->columns = $this->oldTable->getColumns();
    }


    function checkDiferences($newDbfFilePath, $searchRange){
        $this->newTable = new Table($newDbfFilePath);
        $newColumns = $this->newTable->getColumns();
        if ($newColumns != $this->columns)
            throw new Exception('Columns of tables are different.');

        $this->lastUndeletedRecordIndexNewTable = $this->getLastUndeletedRecordIndex($this->newTable);
        if ($this->lastUndeletedRecordIndexNewTable == null)
            throw new Exception('Cannot found an undeleted record in NewTable');

        $this->lastUndeletedRecordIndexOldTable = $this->getLastUndeletedRecordIndex($this->oldTable);
            if ($this->lastUndeletedRecordIndexOldTable == null)
                throw new Exception('Cannot found an undeleted record in OldTable');
    
        if ($searchRange == null){
            $this->searchRange = $this->newTable->getRecordCount() + $this->oldTable->getRecordCount(); #Numero que abarca la cantidad de registros de ambas tablas, las va a recorrer todas buscando diferencias
        } else{
            $this->searchRange = $searchRange;
        }
        echo "Checking new records \n";
        $this->checkNewRecords();
        echo "\n";
        echo "\n";
        echo "Checking deleted records \n";
        $this->checkDeletedRecords();

    }

    function checkNewRecords(){
        $i = $this->lastUndeletedRecordIndexNewTable; #Indice de los ultimos registros no eliminados.

        $newRecordsAnalized = 0; #Variables para analizar tantos registros como diga el searchRange
        $oldRecordsAnalized = 0;

        while($i >= 0 and $newRecordsAnalized < $this->searchRange) #Mientras el indice sea valido y no haya analizado tantos registros como el searchRange
        {
            $recordNewTable = $this->newTable->pickRecord($i);
            $i--;
            if($recordNewTable->isDeleted()){ #Si el registro estaba eliminado
                continue; #Continuo al siguiente ciclo, sin descontar registros por analizar del searchRange
            }
            $newRecordsAnalized++; #Descuento registros por analizar del serachRange
            $isNew = true;
            $j = $this->lastUndeletedRecordIndexOldTable;
            while($j >= 0 and $oldRecordsAnalized < $this->searchRange*2){
                $recordOldTable = $this->oldTable->pickRecord($j);
                $j--;
                if($recordOldTable->isDeleted()){
                    continue;
                }
                $oldRecordsAnalized++;
                if ($this->equals($recordNewTable, $recordOldTable)){
                    $isNew = false;
                    break; #Los registros eran iguales, quiere decir que no es nuevo, salgo del while.
                }
            }
            if ($isNew and !in_array($recordNewTable, $this->newRecordsFound))
               $this->newRecordsFound[] = $recordNewTable;
            $oldRecordsAnalized = 0;
        }
    }


    function checkDeletedRecords(){
        $j = $this->lastUndeletedRecordIndexOldTable;#Indice de los ultimos registros no eliminados.

        $newRecordsAnalized = 0; #Variables para analizar tantos registros como diga el searchRange
        $oldRecordsAnalized = 0;

        while($j >= 0 and $oldRecordsAnalized < $this->searchRange) #Mientras el indice sea valido y no haya analizado tantos registros como el searchRange
        {
            $recordOldTable = $this->oldTable->pickRecord($j);
            $j--;
            if($recordOldTable->isDeleted()){ #Si el registro estaba eliminado
                continue; #Continuo al siguiente ciclo, sin descontar registros por analizar del searchRange
            }
            $oldRecordsAnalized++; #Descuento registros por analizar del serachRange
            $isDeleted = true;
            $i = $this->lastUndeletedRecordIndexNewTable; 
            while($i >= 0 and $newRecordsAnalized < $this->searchRange*2){
                $recordNewTable = $this->newTable->pickRecord($i);
                $i--;
                if($recordNewTable->isDeleted()){
                    continue;
                }
                $newRecordsAnalized++;
                if ($this->equals($recordNewTable, $recordOldTable)){
                    $isDeleted = false;
                    break; #Los registros eran iguales, quiere decir que no es nuevo, salgo del while.
                }
            }
            if ($isDeleted and !in_array($recordOldTable, $this->deletedRecordsFound))
                $this->deletedRecordsFound[] = $recordOldTable;
            
            $newRecordsAnalized = 0;
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

    function printNewRecordsFound(){
        if (count($this->newRecordsFound) == 0){
            echo "Any new record was found.\n";
        }
        else
        {
            echo "New records found:\n";
            foreach($this->newRecordsFound as $newRecord)
                $this->printRecord($newRecord);
        }
    }

    function printDeletedRecordsFound(){
        if (count($this->deletedRecordsFound) == 0){
            echo "Any deleted record was found.\n";
        }
        else
        {
            echo "Deleted records found:\n";
            foreach($this->deletedRecordsFound as $reletedRecord)
                $this->printRecord($reletedRecord);
        }
    }

    function printRecord($record){
        foreach($this->columns as $column){
            echo $record->$column." ";
        }
        echo "\n";
    }
}
?>