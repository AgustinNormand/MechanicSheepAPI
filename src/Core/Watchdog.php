<?php

namespace API\Core;

use API\Core\Config;
use API\Core\Comparator;
use API\Core\Log;

class Watchdog{

    private $pathToFile;

    private $fileNames;

    private $limits;

    private $modifyDates = [];

    private $comparatorClientes;
    private $comparatorVehiculos;
    private $comparatorDetalles;
    private $comparatorTrabajos;

    private $onlyHistoricals;

    function __construct(){
        $config = Config::getInstance();
        $this->pathToFile = $config->get("DBF_FILES_PATH");
        $this->onlyHistoricals = ($config->get("ONLY_HISTORICAL_RECORDS") == "true");

        $this->limits = 
            [
                'Vehiculos' => 
                    [
                        $config->get("RANGO_IZQ_VEHICULOS"),
                        $config->get("RANGO_DER_VEHICULOS")
                    ],
                'Clientes' => 
                    [
                        $config->get("RANGO_IZQ_VEHICULOS"),
                        $config->get("RANGO_DER_VEHICULOS")
                    ],
                'Detalles' => 
                    [
                        $config->get("RANGO_IZQ_VEHICULOS"),
                        $config->get("RANGO_DER_VEHICULOS")
                    ],
                'Trabajos' => 
                    [
                        $config->get("RANGO_IZQ_VEHICULOS"),
                        $config->get("RANGO_DER_VEHICULOS")
                    ],
            ];
        
        $this->fileNames = 
            [
                'Vehiculos' => $config->get("DBF_VEHICULOS_NAME"),
                'Clientes' => $config->get("DBF_CLIENTES_NAME"),
                'Trabajos' => $config->get("DBF_TRABAJOS_NAME"),
                'Detalles' => $config->get("DBF_DETALLES_NAME"),
            ];

        $this->initializeModifyDates();
        $this->initializeComparators();
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
        $this->comparatorClientes = new Comparator();
        $this->comparatorVehiculos = new Comparator();
        if(!$this->onlyHistoricals)
        {
            $this->comparatorDetalles = new Comparator();
            $this->comparatorTrabajos = new Comparator();
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