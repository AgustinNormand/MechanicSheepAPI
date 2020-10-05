<?php 

require "./vendor/autoload.php";
require_once('Database.php');

use XBase\Table;
use XBase\WritableTable; #BORRAR ESTO

class gestor{
    private $database;
    var $table = null;
    var $columns = null;
    
    private $databaseName;

    private $atributes;
   
    function __construct($databaseType){
        $this->databaseName = $databaseType;
        $this->database = new Database($databaseType);
        $this->atributes = $this->database->getAtributes();
        $this->columns = $this->database->getColumnNames();
    }

    function openDatabase(){
        $this->table = new Table($this->database->getPathToDatabase());
    }

    function closeDatabase(){
        $this->table->close();
    }

    function getCantidadRegistros(){
        $this->openDatabase();
        if (!is_null($this->table)){
            $recordCount = $this->table->getRecordCount();
            $this->closeDatabase();
            return $recordCount;
        }
        
    }

    function getColumnasFiltradas(){
        if (!is_null($this->columns)){
            return implode(', ', $this->columns);
        }
    }

    function getColumnasReales(){
        $this->openDatabase();
        if (!is_null($this->table)){
            $this->closeDatabase();
            $columnasReales = implode(', ', $this->table->getColumns());
            return $columnasReales;
        }
    }


    function concatenateRecord(&$data, $record, $j){
        foreach($this->atributes as $atribute){
            $humanAtributeName = $this->database->getHumanAtributeName($atribute);
            $data[$j][$humanAtributeName] = $record->$atribute;
        }
    }

    function search($atribute, $valueToSearch){
        $this->openDatabase();
        $j = 0;
        $data = array(
            #array()
        );
        
        $atribute = $this->database->getMachineAtributeName($atribute);
        if (!is_null($atribute)){
            while ($record = $this->table->nextRecord()) {
                $value = $record->$atribute;
                if($value == $valueToSearch){
                    $this->concatenateRecord($data, $record, $j);       
                    $j++;
                }
            }
        }
        $this->closeDatabase();
        return $data;
    }

    function showAllRecords(){
        $this->openDatabase();
        $j = 0;
        $data = array(
            array()
        );
        while ($record = $this->table->nextRecord()) {
            $this->concatenateRecord($data, $record, $j);       
            $j++;
        }
        $this->closeDatabase();
        return $data;
    }

    function printData($data){
        foreach($data as $record){
            $i = 0;
            foreach ($record as $key=>$valor){
                echo $key;
                echo ': ';
                echo $valor;
                echo '<br>';
            }
            echo '<br>';
        }
    }

    function eraseSensibleInformation(){
        $table = new WritableTable('../Databases/sermae2.dbf');
        $table->openWrite();

        $sensibleFields = array(
        );

        while ($record = $table->nextRecord()) {
            foreach($sensibleFields as $sensibleField){
                $record->$sensibleField = 10;
                $table->writeRecord();
            }
        }
    }

    function exportCsv(){
        $data = array();
        $fp = fopen('../Databases/CSV_Files/'.$this->databaseName.'.csv','w');
        $this->openDatabase();
        $recordCount = 0;
        while ($record = $this->table->nextRecord()) {
            $i = 0;
            foreach($this->atributes as $atribute){
                $data[$i++] = $record->$atribute;
            }
            fputcsv($fp, $data);
            $recordCount++;
        }
        echo "Counted records: ".$recordCount;
        echo "<br>";
        $this->closeDatabase();
        fclose($fp);

    }
}
?>