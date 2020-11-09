<?php

namespace API\Core\Comparators;

use API\Core\Database\Tables\Table;
use API\Core\Config;
use \Exception;

use API\Core\Log;

class Comparator
{
    private $databaseName;
    private $validatorName;

    private $oldTable = null;
    private $newTable = null;

    private $leftSearchRange = null;

    protected $newRecordsFound = [];
    protected $deletedRecordsFound = [];
    protected $modifiedRecordsFound = [];

    private $dbfFilePath = null;

    function __construct($databaseName)
    {
        $this->databaseName = $databaseName;
        $this->validatorName = "API\\Core\\Validators\\Validator{$databaseName}";
        $this->leftSearchRange = Config::getInstance()->get("RANGO_IZQ_". strtoupper($databaseName));
        $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
        $dbfName = Config::getInstance()->get("DBF_". strtoupper($this->databaseName) ."_NAME");
        $this->dbfFilePath = $dbfPath . $dbfName;
    }
    
    function setCheckpoint(){
        copy($this->dbfFilePath, $this->dbfFilePath.'.bk');
    }


    function checkDiferences(){
        $this->oldTable = new Table($this->dbfFilePath.'.bk', $this->databaseName);
        $this->newTable = new Table($this->dbfFilePath, $this->databaseName);

        if(!$this->newTable->isSameType($this->oldTable))
        {
            Log::error("Columns of tables are different.", [$this->newTable, $this->oldTable]);
            throw new Exception('Columns of tables are different.');
        }

        Log::debug("Verificando registros nuevos.");
        $this->checkNewRecords();
        Log::debug("Verificando registros modificados o eliminados.");
        $this->checkModifiedAndDeletedRecords();

        $this->setCheckpoint();

        $this->closeDatabases();
    }

    function checkNewRecords(){
        $index = $this->oldTable->getLastRecordIndex();
        $this->addNewRecords($this->newTable->getUndeletedRecordsAfterIndex($index));
    }

    function checkModifiedAndDeletedRecords()
    {
        $index = $this->oldTable->getLastUndeletedRecordIndex();
        $oldRecordsAnalized = 0;
        while($index>=0 and (($oldRecordsAnalized <= $this->leftSearchRange) or $this->leftSearchRange == 0))
        {
            $recordOldTable = $this->oldTable->getUndeletedRecord($index);
            if(!is_null($recordOldTable))
            {
                $oldRecordsAnalized++;
                $recordNewTable = $this->newTable->getRecord($index);
                if($recordNewTable->isDeleted())
                    $this->addDeletedRecord($recordNewTable);
                else
                    if(!$recordNewTable->equals($recordOldTable))
                        $this->addModifiedRecord($recordOldTable, $recordNewTable); 
            }
            $index--;
        }
    }
    
    function exists($recordToSearch, $array){
        $result = false;
        if($array == $this->modifiedRecordsFound)
        {
            foreach($array as $record)
                if ($recordToSearch->equals($record['from'])){
                    $result = true;
                break;
                }
        }
        else
            foreach($array as $record)
                if ($recordToSearch->equals($record)){
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
        if(!$this->exists($from, $this->modifiedRecordsFound) and $this->validatorName::isValid($from)){
            $index = count($this->modifiedRecordsFound);
            $this->modifiedRecordsFound[$index]["from"] = $from;
            $this->modifiedRecordsFound[$index]["to"] = $to;
            Log::info("Modified from: ", [$from]);
            Log::info("Modified to: ", [$to]);
        }
        else
            if(!$this->validatorName::isValid($from) and $this->validatorName::isValid($to))
                $this->addNewRecord($to);
    }

    function addNewRecord($record){
        if (!$this->exists($record, $this->newRecordsFound) and $this->validatorName::isValid($record)){
            $this->newRecordsFound[] = $record;
            Log::info("New: ", [$record]);
        }
    }

    function addDeletedRecord($record){
        if (!$this->exists($record, $this->deletedRecordsFound) and $this->validatorName::isValid($record)){
            $this->deletedRecordsFound[] = $record;
            Log::info("Deleted: ", [$record]);
        }
    }

    function addNewRecords($records)
    {
        foreach($records as $record)
        {
            $this->addNewRecord($record);
        }
    }

    function resetAcumulatedRecords() ##Esto hay que hacerlo mucho mejor
    {
        $this->newRecordsFound = [];
        $this->deletedRecordsFound = [];
        $this->modifiedRecordsFound = [];
    }
}
?>