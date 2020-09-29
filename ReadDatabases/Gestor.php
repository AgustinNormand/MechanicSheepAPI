<?php 

require "./vendor/autoload.php";
require_once('Database.php');

use XBase\Table;
use XBase\WritableTable; #BORRAR ESTO

class gestor{
    private $database;
    var $table = null;
    var $columns = null;

    private $atributes;
   
    function __construct($databaseType){
        $this->database = new Database($databaseType);
        $this->atributes = $this->database->getAtributes();
        $this->columns = $this->database->getColumnNames();

        $this->table = new Table($this->database->getPathToDatabase());

        
    }

    function getCantidadRegistros(){
        if (!is_null($this->table)){
            return $this->table->getRecordCount();
        }
    }

    function getColumnasFiltradas(){
        if (!is_null($this->columns)){
            return implode(', ', $this->columns);
        }
    }

    function getColumnasReales(){
        if (!is_null($this->table)){
            return implode(', ', $this->table->getColumns());
        }
    }


    function storeRecord(&$data, $record, $j){
        foreach($this->atributes as $atribute){
            $humanAtributeName = $this->database->getHumanAtributeName($atribute);
            $data[$j][$humanAtributeName] = $record->$atribute;
        }
    }

    function search($atribute, $valueToSearch){
        $j = 0;
        $data = array(
            array()
        );
        
        $atribute = $this->database->getMachineAtributeName($atribute);
        if (!is_null($atribute)){
            while ($record = $this->table->nextRecord()) {
                $value = $record->$atribute;
                if($value == $valueToSearch){
                    $this->storeRecord($data, $record, $j);       
                    $j++;
                }
            }
        }
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
            'sersub',
            'serpre',
            'sergan',
        );

        while ($record = $table->nextRecord()) {
            foreach($sensibleFields as $sensibleField){
                $record->$sensibleField = 10;
                $table->writeRecord();
            }
        }
    }
}
?>