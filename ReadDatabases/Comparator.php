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
        $this->oldTable = new Table($oldDbfFilePath);
        $this->columns = $this->oldTable->getColumns();
    }


    function checkDiferences($newDbfFilePath){
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
    
        #print_r());
        $record = $this->oldTable->pickRecord(0);
        #$record = $this->newTable->pickRecord($this->lastUndeletedRecordIndexNewTable);
        #foreach($this->oldTable->getColumns() as $column)
        #    echo $record->$column;
        #echo $this->lastUndeletedRecordIndexOldTable;

        echo '<p>';
        echo 'Checking new records';
        echo '<br>';
        $this->checkNewRecords();
        echo '</p>';

        echo '<p>';
        echo 'Checking deleted records';
        echo '<br>';
        #$this->checkDeletedRecords();
        echo '</p>';

    }

    function checkNewRecords(){
        $i = $this->lastUndeletedRecordIndexNewTable; #Indice de los ultimos registros no eliminados.
        $j = $this->lastUndeletedRecordIndexOldTable;

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
            while($j >= 0 and $oldRecordsAnalized < $this->searchRange){
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
                if ($isNew){
                    echo 'New Record Found';
                    echo '<br>';
                    #foreach($this->newTable->getColumns() as $column)
                    #    echo $recordNewTable -> $column;
                    #print_r($recordNewTable);
                }
            }
            $oldRecordsAnalized = 0;
        }
    }


    function checkDeletedRecords(){
        $i = $this->lastUndeletedRecordIndexNewTable; #Indice de los ultimos registros no eliminados.
        $j = $this->lastUndeletedRecordIndexOldTable;

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
            while($i >= 0 and $newRecordsAnalized < $this->searchRange){
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
                if ($isDeleted){
                    echo 'Deleted Record Found';
                    echo '<br>';
                    #foreach($this->newTable->getColumns() as $column)
                    #    echo $recordNewTable -> $column;
                    #print_r($recordNewTable);
                }
            }
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

    function getDiferences(){
    }
}

$cp = new Comparator();
$cp->setCheckpoint('../Databases/sermae2.dbf');
$cp->checkDiferences('../Databases/sermae2.dbf');














/*
    function checkDeletedRecords(){
        $i = $this->lastRecordNewTable;
        $lastRecordInSearchRangeNewTable = $this->lastRecordNewTable - $this->searchRange;

        $j = $this->lastRecordOldTable;
        $lastRecordInSearchRangeOldTable = $this->lastRecordOldTable - $this->searchRange;

        for($j; $j <= $lastRecordInSearchRangeOldTable; $j--){
            $recordOldTable = $this->oldTable->pickRecord($j);
            $isDeleted = true;
            for($i; $i <= $lastRecordInSearchRangeNewTable; $i--){
                $recordNewTable = $this->newTable->pickRecord($i);
                if ($this->equals($recordNewTable, $recordOldTable)){
                    $isDeleted = false;
                    break;
                }
                if ($isDeleted){
                    echo 'Deleted Record Found';
                    echo '<br>';
                    print_r($recordOldTable);
                }
            }
        }
    }



        function checkNewRecords(){
        $i = $this->lastRecordNewTable;
        $lastRecordInSearchRangeNewTable = $this->lastRecordNewTable - $this->searchRange;

        $j = $this->lastRecordOldTable;
        $lastRecordInSearchRangeOldTable = $this->lastRecordOldTable - $this->searchRange;
        for($i; $i <= $lastRecordInSearchRangeNewTable; $i--){
            $recordNewTable = $this->newTable->pickRecord($i);
            $isNew = true;
            for($j; $j <= $lastRecordInSearchRangeOldTable; $j--){
                $recordOldTable = $this->oldTable->pickRecord($j);
                if ($this->equals($recordNewTable, $recordOldTable)){
                    $isNew = false;
                    break;
                }
                if ($isNew){
                    echo 'New Record Found';
                    echo '<br>';
                    print_r($recordNewTable);
                }
            }
        }
    }
*/
?>