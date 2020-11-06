<?php
    require '..' . DIRECTORY_SEPARATOR .'bootstrap.php';

    use API\Core\Config;

    use XBase\Table;

    use API\Core\Database\Updaters\ReflectChangesTrabajos;
    use API\Core\Database\Updaters\ReflectChangesClientes;

    use API\Core\Log;

    class Migrator
    {
        #private $reflectChangesClientes;
        #private $reflectChangesVehiculos;
        #private $reflectChangesDetalles;
        private $reflectChangesTrabajos;

        public function __construct()
        {
            Log::Info("Begin migraion");
            $this->reflectChangesClientes = new ReflectChangesClientes;
            $this->reflectChangesTrabajos = new ReflectChangesTrabajos;

            $config = Config::getInstance();
            #$pathToDatabases = $config->get("DBF_FILES_PATH");
            $pathToDatabases = __DIR__."/../../DBS_FOR_TESTS/DBS_UNTOUCHED/";
            
            $databasesNames = 
            [
                #'Vehiculos' => $config->get("DBF_VEHICULOS_NAME"),
                'Clientes' => $config->get("DBF_CLIENTES_NAME"),
                #'Trabajos' => $config->get("DBF_TRABAJOS_NAME"),
                #'Detalles' => $config->get("DBF_DETALLES_NAME"),
            ];

            foreach($databasesNames as $databaseName)
            {
                $records = [];
            $table = new Table($pathToDatabases . $databaseName/*, null, 'CP1252'*/);
                $key = array_search($databaseName, $databasesNames);
                $validatorName = "API\\Core\\Validators\\Validator{$key}";
                
                for($i=0; $i < $table->getRecordCount(); $i++)
                {
                    $record = $table->pickRecord($i);
                    #if($record->clinom == "WLADER RUBEN"){
                        #var_dump($record);
                        #echo $record->cliape;
                        #die;
                    #}
                    if(!$record->isDeleted() and $validatorName::isValid($record)){
                        $records[] = $record;
                    }
                } 
                
                $reflectChangesName = "reflectChanges" . $key;
                $this->$reflectChangesName->newRecords($records);
            }
        }
    }

    $mg = new Migrator;