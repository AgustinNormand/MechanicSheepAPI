<?php

namespace API\Core;

use API\Core\Config;
use API\Core\Log;

use API\Core\Comparators\ComparatorBase;
use API\Core\Comparators\ComparatorTrabajos;
use API\Core\Comparators\ComparatorVehiculos;
use API\Core\Comparators\ComparatorDetalles;
use API\Core\Comparators\ComparatorClientes;

use API\Core\Database\Updaters\ReflectChanges;
use API\Core\Database\Updaters\ReflectChangesTrabajos;
use API\Core\Database\Updaters\ReflectChangesClientes;

class Watchdog{

    private $pathToFile;

    private $fileNames;

    private $limits;

    private $modifyDates = [];

    private $comparatorClientes;
    private $comparatorVehiculos;
    private $comparatorDetalles;
    private $comparatorTrabajos;

    private $reflectChangesClientes;
    private $reflectChangesVehiculos;
    private $reflectChangesDetalles;
    private $reflectChangesTrabajos;

    private $onlyHistoricals;

    function __construct(){
        
        $config = Config::getInstance();
        $this->pathToFile = $config->get("DBF_FILES_PATH");
        $this->onlyHistoricals = ($config->get("ONLY_HISTORICAL_RECORDS") == "true");

        $this->limits = 
            [
                'Vehiculos' => 
                    [
                        $config->get("RANGO_IZQ_VEHICULOS") == 0 ? null : $config->get("RANGO_IZQ_VEHICULOS"), 
                        $config->get("RANGO_DER_VEHICULOS") == 0 ? null : $config->get("RANGO_DER_VEHICULOS")  
                    ],
                'Clientes' => 
                    [
                        $config->get("RANGO_IZQ_CLIENTES") == 0 ? null : $config->get("RANGO_IZQ_CLIENTES"),
                        $config->get("RANGO_DER_CLIENTES") == 0 ? null : $config->get("RANGO_DER_CLIENTES")  
                    ],
                'Detalles' => 
                    [
                        $config->get("RANGO_IZQ_DETALLES") == 0 ? null : $config->get("RANGO_IZQ_DETALLES"),
                        $config->get("RANGO_DER_DETALLES") == 0 ? null : $config->get("RANGO_DER_DETALLES")  
                    ],
                'Trabajos' => 
                    [
                        $config->get("RANGO_IZQ_TRABAJOS") == 0 ? null : $config->get("RANGO_IZQ_TRABAJOS"),
                        $config->get("RANGO_DER_TRABAJOS") == 0 ? null : $config->get("RANGO_DER_TRABAJOS")        
                    ],
            ];

            #var_dump($this->limits);
        
        $this->fileNames = 
            [
                'Vehiculos' => $config->get("DBF_VEHICULOS_NAME"),
                'Clientes' => $config->get("DBF_CLIENTES_NAME"),
                'Trabajos' => $config->get("DBF_TRABAJOS_NAME"),
                'Detalles' => $config->get("DBF_DETALLES_NAME"),
            ];

        $this->initializeModifyDates();
        $this->initializeComparators();
        $this->initializeReflectChanges();
    }

    function initializeModifyDates(){
        foreach($this->fileNames as $fileName){
            $filePath = $this->pathToFile.$fileName;
            if (file_exists($filePath))
                $this->modifyDates[$fileName] = filemtime($filePath);
            else 
                Log::error("initializeModifyDates - File not found {$filePath}");
        }
    }

    function initializeComparators(){
        $this->comparatorClientes = new ComparatorClientes();
        $this->comparatorVehiculos = new ComparatorVehiculos();
        if(!$this->onlyHistoricals)
        {
            $this->comparatorDetalles = new ComparatorBase();
            $this->comparatorTrabajos = new ComparatorBase();
        } else
        {
            $this->comparatorDetalles = new ComparatorDetalles();
            $this->comparatorTrabajos = new ComparatorTrabajos();
        }
        
        $this->comparatorClientes->setCheckpoint($this->pathToFile.$this->fileNames['Clientes']);
        $this->comparatorVehiculos->setCheckpoint($this->pathToFile.$this->fileNames['Vehiculos']);
        $this->comparatorDetalles->setCheckpoint($this->pathToFile.$this->fileNames['Detalles']);
        $this->comparatorTrabajos->setCheckpoint($this->pathToFile.$this->fileNames['Trabajos']);
    }

    function initializeReflectChanges()
    {
        $this->reflectChangesClientes = new ReflectChangesClientes;
        $this->reflectChangesTrabajos = new ReflectChangesTrabajos;
        $this->reflectChangesVehiculos = new ReflectChanges;
        $this->reflectChangesDetalles = new ReflectChanges;

    }

    function checkModifyDates(){
        foreach($this->fileNames as $fileName){
            $filePath = $this->pathToFile.$fileName;
            if (file_exists($filePath)){
                $modifyDate = filemtime($filePath);
                if ($modifyDate > $this->modifyDates[$fileName]){
                    $this->modifyDetected($fileName);
                    $this->modifyDates[$fileName] = $modifyDate;
                }
                else 
                    if ($modifyDate < $this->modifyDates[$fileName])
                        Log::warning("CheckModifyDates - Fecha de modificación menor a la almacenada: {$fileName}");
            }
        }
    }

    function modifyDetected($fileName){
        $databaseType = array_search($fileName, $this->fileNames);
        Log::debug("ModifyDetected - Nueva modificación detectada en: {$databaseType}");

        $comparatorName = "comparator" . $databaseType;
        $this->$comparatorName->checkDiferences($this->limits[$databaseType][0],$this->limits[$databaseType][1]);

        $reflectChangesName = "reflectChanges" . $databaseType;
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