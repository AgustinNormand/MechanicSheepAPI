<?php

require_once('Comparator.php');

Class Watchdog{

    private $pathToFile = 'C:/Users/Windows/Desktop/Sistema Mechanic Sheep/Core (CREO)/visual/OVEJA/';

    private $fileNames = array(
        'Vehiculos' => 'VEHmae.DBF',
        'Clientes' => 'climae.dbf',
        'Trabajos' => 'SERMAE.DBF',
        'Detalles' => 'sermae2.dbf',
    );

    private $modifyDates = array(
    );

    private $comparatorClientes;
    private $comparatorVehiculos;
    private $comparatorDetalles;
    private $comparatorTrabajos;

    function __construct(){
        $this->initializeModifyDates();
        $this->initializeComparators();
    }

    function checkModifyDates(){
        foreach($this->fileNames as $fileName){
            $filePath = $this->pathToFile.$fileName;
            if (file_exists($filePath)){
                #echo '<p>';
                $modifyDate = filemtime($filePath);
                if ($modifyDate > $this->modifyDates[$fileName]){
                    $this->modifyDetected($fileName);
                    $this->modifyDates[$fileName] = $modifyDate;
                }
                #else 
                    #if ($modifyDate == $this->modifyDates[$fileName])
                        #echo $fileName.' is untouched';
                    #else WARNING
                #echo '</p>';
            } #else WARNING
        }
    }

    function initializeModifyDates(){
        foreach($this->fileNames as $fileName){
            $filePath = $this->pathToFile.$fileName;
            if (file_exists($filePath))
                $this->modifyDates[$fileName] = filemtime($filePath);
            #else WARNING
        }
    }

    function initializeComparators(){
        $this->comparatorClientes = new Comparator();
        $this->comparatorClientes->setCheckpoint($this->pathToFile.$this->fileNames['Clientes']);
        $this->comparatorVehiculos = new Comparator();
        $this->comparatorVehiculos->setCheckpoint($this->pathToFile.$this->fileNames['Vehiculos']);
        $this->comparatorDetalles = new Comparator();
        $this->comparatorDetalles->setCheckpoint($this->pathToFile.$this->fileNames['Detalles']);
        $this->comparatorTrabajos = new Comparator();
        $this->comparatorTrabajos->setCheckpoint($this->pathToFile.$this->fileNames['Trabajos']);
    }

    function modifyDetected($fileName){
        $databaseType = array_search($fileName, $this->fileNames);
        echo "New modify in '.$databaseType.' detected.\n";
        switch($databaseType){
            case 'Clientes':
                $this->comparatorClientes->checkDiferences($this->pathToFile.$fileName, 150);
                $this->comparatorClientes->printNewRecordsFound();
                $this->comparatorClientes->printDeletedRecordsFound();
            break;
            case 'Vehiculos':
                $this->comparatorVehiculos->checkDiferences($this->pathToFile.$fileName, 150);
                $this->comparatorVehiculos->printNewRecordsFound();
                $this->comparatorVehiculos->printDeletedRecordsFound();
            break;
            case 'Detalles':
                $this->comparatorDetalles->checkDiferences($this->pathToFile.$fileName, 150);
                $this->comparatorDetalles->printNewRecordsFound();
                $this->comparatorDetalles->printDeletedRecordsFound();
            break;
            case 'Trabajos':
                $this->comparatorTrabajos->checkDiferences($this->pathToFile.$fileName, 150);
                $this->comparatorTrabajos->printNewRecordsFound();
                $this->comparatorTrabajos->printDeletedRecordsFound();
            break;     
        }
    }

 

}

$wd = new Watchdog();
while (true){
    $wd->checkModifyDates();
    sleep(1);
}
?>