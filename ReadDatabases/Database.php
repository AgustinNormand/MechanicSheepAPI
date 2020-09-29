<?php 

require "./vendor/autoload.php";

use XBase\Table;

class database {
    private $pathToDatabase = null;
    private $atributes;

    function __construct($databaseType){
        switch($databaseType){
            case 'VEHICULOS':
                $this->pathToDatabase = '../Databases/VEHmae.DBF';
                $this->atributes = array(
                    'patente' => 'vehpat',
                    'apellido' => 'vehape',
                    'nombre' => 'vehnom',
                    'marca' => 'vehmar',
                    'modelo' => 'vehmod',
                    'año' => 'vehano',
                    'codigomotor' => 'vehmot',
                    'vin' => 'vehvin',
                    'fechacompra' => 'vehcom',
                );
            break;
            case 'CLIENTES':
                $this->pathToDatabase = '../Databases/climae.dbf';
                $this->atributes = array(
                    'apellido' => 'cliape',
                    'nombre' => 'clinom',
                    'telefono' => 'clitel',
                    'direccion' => 'clidir',
                    'localidad' => 'cliloc',
                    'dni' => 'clidoc',
                    'email' => 'climai',
                );
            break;
            case 'TRABAJOS':
                $this->pathToDatabase = '../Databases/SERMAE.DBF';
                $this->atributes = array(
                  'numero' => 'sernro',
                  'patente' => 'serpat',
                  'modelo' => 'sermod',
                  'marca' => 'sermar',
                  'apellido' => 'serape',
                  'nombre' => 'sernom',
                  'descripcion' => 'seracu1',
                  'tecnico' => 'sertec',
                  'kilometros' => 'serklm',
                  'fecha' => 'serfec',
                  

                  #BORRAR ESTOS
                  
                  'serest' => 'serest',
                  'sercosr' => 'sercosr',
                  'sercosm' => 'sercosm',
                  'sertot' => 'sertot',
                  'sercosg' => 'sercosg',
                  'movcta' => 'movcta',
                  'movcom' => 'movcom',
                  'movtip' => 'movtip',
                  'movsuc' => 'movsuc',
                  'movnro' => 'movnro',
                  'seracu2' => 'seracu2',
                  'seracu3' => 'seracu3',
                  'seracu4' => 'seracu4',
                  'serenc1' => 'serenc1',
                  'serenc2' => 'serenc2',
                  'serenc3' => 'serenc3',
                  'serenc4' => 'serenc4',
                  'serpre' => 'serpre',
                  'serrec' => 'serrec',
                  'serent' => 'serent',
                  'sergar' => 'sergar',
                  'con1' => 'con1',
                  'con2' => 'con2',
                  'con3' => 'con3',
                  'sertel' => 'sertel',
                  'serciva' => 'serciva',
                  'sercom' => 'sercom',
                  'serter' => 'serter',

                  #BORRAR ESTOS
                );
            break;
            case 'DETALLES':
                $this->pathToDatabase = '../Databases/sermae2.dbf';
                $this->atributes = array(
                    'descripcion' => 'serdes',
                    'cantidad' => 'sercan',
                    'numero' => 'sernro',
                    'apellido' => 'serape',
                    'nombre' => 'sernom',
                    'patente' => 'serpat',
                    'codigo' => 'sercod',

                    #BORRAR ESTOS
                    'sergan' => 'sergan',
                    'sersub' => 'sersub',
                    'serpre' => 'serpre',
                    'sercom' => 'sercom',
                    'movcom' => 'movcom',
                    'movtip' => 'movtip',
                    'movsuc' => 'movsuc',
                    'movnro' => 'movnro',
                    'seriva' => 'seriva',
                    'serdec' => 'serdec',
                    'serccom' => 'serccom',
                    'serctip' => 'serctip',
                    'sercsuc' => 'sercsuc',
                    'sercnro' => 'sercnro',
                    #BORRAR ESTOS
                  );
            break;
        }
    }

    function getPathToDatabase(){
        return $this->pathToDatabase;
    }

    function getAtributes(){
        return $this->atributes;
    }

    function getMachineAtributeName($atribute){
        return $this->atributes[$atribute];
    }

    function getHumanAtributeName($atribute){
        return array_search($atribute, $this->atributes);
    }

    function getColumnNames(){
        return array_keys($this->atributes);
    }
}
?>