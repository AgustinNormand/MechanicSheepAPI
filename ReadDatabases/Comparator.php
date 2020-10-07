<?php

require "./vendor/autoload.php";
require_once('Database.php');
use XBase\Table;

Class Comparator
{

    var $oldTable = null;
    private $oldTablePath = null;

    var $newTable = null;

    private $leftSearchRange = null;
    private $rightSearchRange = null;

    private $columns;

    private $lastUndeletedRecordIndexNewTable = null;
    private $lastUndeletedRecordIndexOldTable = null;

    private $newRecordsFound = array();
    private $deletedRecordsFound = array();

    private $dbfFilePath = null;
    
    function __construct(){
    }

    function setCheckpoint($dbfFilePath){
        $this->dbfFilePath = $dbfFilePath;
        copy($dbfFilePath, $dbfFilePath.'.bk');
    }


    function checkDiferences($leftSearchRange, $rightSearchRange){
        $this->oldTable = new Table($this->dbfFilePath.'.bk');
        $this->newTable = new Table($this->dbfFilePath);

        $this->columns = $this->oldTable->getColumns();
        $newColumns = $this->newTable->getColumns();

        if ($newColumns != $this->columns)
            throw new Exception('Columns of tables are different.');

        $this->lastUndeletedRecordIndexNewTable = $this->getLastUndeletedRecordIndex($this->newTable);
        if ($this->lastUndeletedRecordIndexNewTable == null)
            throw new Exception('Cannot found an undeleted record in NewTable');

        $this->lastUndeletedRecordIndexOldTable = $this->getLastUndeletedRecordIndex($this->oldTable);
            if ($this->lastUndeletedRecordIndexOldTable == null)
                throw new Exception('Cannot found an undeleted record in OldTable');
    
        $this->leftSearchRange = $leftSearchRange;
        $this->rightSearchRange = $rightSearchRange;

        echo "Checking new records \n";
        $this->checkNewRecords();
        echo "\n";
        echo "\n";
        echo "Checking deleted records \n";
        $this->checkDeletedRecords();

        $this->setCheckpoint($this->dbfFilePath);

        $this->closeDatabases();
    }

    function checkNewRecords(){
        $i = $this->lastUndeletedRecordIndexNewTable; #Indice de los ultimos registros no eliminados.

        $newRecordsAnalized = 0; #Variables para analizar tantos registros como diga el searchRange

        while($i >= 0 and ($newRecordsAnalized <= $this->leftSearchRange or $this->leftSearchRange == null))
        {
            $recordNewTable = $this->newTable->pickRecord($i);
            
            if(!$recordNewTable->isDeleted()){ #Si el registro estaba eliminado
                $newRecordsAnalized++; #Descuento registros por analizar del serachRange
                $isNew = $this->buscarAlternadamente($recordNewTable, $i, $this->oldTable, $this->lastUndeletedRecordIndexOldTable, False);
                $j = $this->lastUndeletedRecordIndexOldTable;
                
                if ($isNew == 0 and !$this->exists($recordNewTable, $this->newRecordsFound))
                    $this->newRecordsFound[] = $recordNewTable;
            }
            $i--;
        }
    }

    function checkDeletedRecords(){
        $j = $this->lastUndeletedRecordIndexOldTable;#Indice de los ultimos registros no eliminados.

        $oldRecordsAnalized = 0;

        while($j >= 0 and ($oldRecordsAnalized <= $this->leftSearchRange or $this->leftSearchRange == null)) #Mientras el indice sea valido y no haya analizado tantos registros como el searchRange
        {
            $recordOldTable = $this->oldTable->pickRecord($j);
            
            if(!$recordOldTable->isDeleted()){  
                $oldRecordsAnalized++; #Descuento registros por analizar del serachRange

                $isDeleted = $this->buscarAlternadamente($recordOldTable, $j, $this->newTable, $this->lastUndeletedRecordIndexNewTable, True);

                if (($isDeleted == 2 or $isDeleted == 0) and !$this->exists($recordOldTable, $this->deletedRecordsFound))
                    $this->deletedRecordsFound[] = $recordOldTable;
            }
            $j--;
        }
    }

    function buscarAlternadamente($recordTablaOrigen, $indiceRegistro, $tablaDestino, $lastUndeletedRecordIndexTablaDestino, $analizeDeleted){
        $result = 0; #0=NoLoEncontró 1=LoEncontró 2=LoEncontróDeLosEliminados

        $recordsAnalized = 0;

        $indiceSuperior = $indiceRegistro - 1; #Tiende a cero
        $indiceInferior = $indiceRegistro; #Tiende al mayor registro de la tabla

        if ($analizeDeleted)
            $limiteInferior = $tablaDestino->getRecordCount() - 1;
        else
            $limiteInferior = $lastUndeletedRecordIndexTablaDestino;
            

        while(($indiceSuperior >= 0 or $indiceInferior <= $limiteInferior) and ($recordsAnalized <= $this->rightSearchRange or $this->rightSearchRange == null) and $result == 0){
            if ($indiceSuperior >= 0 and $result == 0){
                $recordTablaDestino = $tablaDestino->pickRecord($indiceSuperior);
                $isDeleted = $recordTablaDestino->isDeleted();
                if(!$isDeleted or $analizeDeleted){
                    if ($this->equals($recordTablaDestino, $recordTablaOrigen))
                        if($isDeleted)
                            $result = 2;
                        else
                            $result = 1;
                    $recordsAnalized++;
                }
                $indiceSuperior--;
            }
            if ($indiceInferior <= $limiteInferior and $result == 0){
                $recordTablaDestino = $tablaDestino->pickRecord($indiceInferior);
                $isDeleted = $recordTablaDestino->isDeleted();
                if(!$recordTablaDestino->isDeleted() or $analizeDeleted){
                    if ($this->equals($recordTablaDestino, $recordTablaOrigen))
                        if($isDeleted)
                        $result = 2;
                    else
                        $result = 1;
                    $recordsAnalized++;
                }
                $indiceInferior++;
            }
        }
        return $result;
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