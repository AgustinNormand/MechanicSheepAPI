<?php
    require __DIR__. '/../bootstrap.php';

    use API\Core\Config;
    use API\Core\Enum\DatabaseNames;
    use API\Core\Database\Tables\Table;

    use API\Core\Log;

    class Migrator
    {
        public function __construct()
        {
            $databaseNames = DatabaseNames::all();
            $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
            foreach($databaseNames as $databaseName)
            {
                Log::Info("Begin migration of {$databaseName}");
                $validatorName = "API\\Core\\Validators\\Validator{$databaseName}";
                $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
                $fullPath = $dbfPath . $dbfName;
                Log::Info("File located in {$fullPath}");
                $table = new Table($fullPath, $databaseName);
                $undeletedRecords = $table->getAllUndeletedRecords();
                $validRecords = [];
                foreach($undeletedRecords as $undeletedRecord)
                    if($validatorName::isValid($undeletedRecord))
                        $validRecords[] = $undeletedRecord;
                /* */
                //echo count($validRecords);
                //echo PHP_EOL;
                //continue;
                /** */
                $reflectChangesName = "reflectChanges" . $databaseName;
                $reflectChangesClassName = "API\\Core\\Database\\Updaters\\" . ucfirst($reflectChangesName);
                $this->$reflectChangesName = new $reflectChangesClassName;
                $this->$reflectChangesName->newRecords($validRecords);
                Log::Info("Finish migration of {$databaseName}");
            }
        }
    }

    $mg = new Migrator;