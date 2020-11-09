<?php

namespace API\Core;

use API\Core\Config;
use API\Core\Log;

use API\Core\Comparators\Comparator;

use API\Core\Enum\DatabaseNames;


class Watchdog{

    private $databaseNames;

    private $modifyDates = [];

    private $filePaths;

    function __construct(){
        $this->databaseNames = DatabaseNames::all();
        $this->initializeFilePaths();
        $this->initializeModifyDates();
        $this->initializeComparators();
        $this->initializeReflectChanges();
    }

    function initializeFilePaths()
    {
        $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
        foreach($this->databaseNames as $databaseName){  
            $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
            $this->filePaths[$databaseName] = $dbfPath . $dbfName;
        }
    }

    function initializeModifyDates(){
        foreach($this->databaseNames as $databaseName){
            $filePath = $this->filePaths[$databaseName];
            if (file_exists($filePath))
                $this->modifyDates[$databaseName] = filemtime($filePath);
            else 
                Log::error("initializeModifyDates - File not found {$filePath}");
        }
    }

    function initializeComparators(){
        foreach($this->databaseNames as $databaseName){
            $comparatorName = "comparator" . $databaseName;
            $this->$comparatorName = new Comparator($databaseName);
            $this->$comparatorName->setCheckpoint();
        }
    }

    function initializeReflectChanges()
    {
        foreach($this->databaseNames as $databaseName){
            $reflectChangesName = "reflectChanges" . $databaseName;
            $reflectChangesClassName = "API\\Core\\Database\\Updaters\\" . ucfirst($reflectChangesName);
            $this->$reflectChangesName = new $reflectChangesClassName;
        }
    }

    function checkModifyDates(){
        foreach($this->databaseNames as $databaseName)
        {
            $filePath = $this->filePaths[$databaseName];
            if (file_exists($filePath))
            {
                $modifyDate = filemtime($filePath);
                if ($modifyDate > $this->modifyDates[$databaseName])
                {
                    $this->modifyDetected($databaseName);
                    $this->modifyDates[$databaseName] = $modifyDate;
                }
                else 
                    if ($modifyDate < $this->modifyDates[$databaseName])
                    Log::warning("CheckModifyDates - Fecha de modificación menor a la almacenada: {$databaseName}");
            } 
            else
            Log::warning("CheckModifyDates - FileNotExists: {$filePath}");
        }
    }

    function modifyDetected($databaseName){
        Log::debug("ModifyDetected - Nueva modificación detectada en: {$databaseName}");
        #var_dump($this);
        #die;

        $comparatorName = "comparator" . $databaseName;
        $this->$comparatorName->checkDiferences();

        $reflectChangesName = "reflectChanges" . $databaseName;
        $this->$reflectChangesName->deletedRecords($this->$comparatorName->getAcumulatedDeletedRecordsFound());
        $this->$reflectChangesName->newRecords($this->$comparatorName->getAcumulatedNewRecordsFound());
        $this->$reflectChangesName->modifiedRecords($this->$comparatorName->getAcumulatedModifiedRecordsFound());
        $this->$comparatorName->resetAcumulatedRecords();
    }

    public function loopForever()
    {
        $sleepTime = Config::getInstance()->get("VERIFY_MODIFICATIONS_TIMER");
        while (true){
            $this->checkModifyDates();
            sleep($sleepTime);
        }
    }
}

?>