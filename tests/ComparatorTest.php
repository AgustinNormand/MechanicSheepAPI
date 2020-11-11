<?php 
    declare(strict_types=1);

    require __DIR__ . '/../src/bootstrap.php';

    use PHPUnit\Framework\TestCase;

    use API\Core\Enum\DatabaseNames;
    use API\Core\Config;
    use API\Core\Comparators\Comparator;

use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\fileExists;

class TestTest extends TestCase
    {
        const DBS_MODIFIED_PATH = '/home/agustin/Test/MechanicSheepAPI/DATABASES/DBS_MODIFIED/';

        const DBS_UNTOUCHED_PATH ='/home/agustin/Test/MechanicSheepAPI/DATABASES/DBS_UNTOUCHED/';
        
        private static $databaseNames;

        private static $filePaths;


        public static function setUpBeforeClass(): void
        {   
            self::$databaseNames = DatabaseNames::all();
            $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
            foreach(self::$databaseNames as $databaseName){  
                $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
                self::$filePaths[$databaseName] = $dbfPath . $dbfName;
            }
        }

        public static function tearDownAfterClass(): void
        {
            
        }

        protected function setUp() : void
        {
            $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
            foreach(self::$databaseNames as $databaseName){ 
                $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
                copy(self::DBS_UNTOUCHED_PATH . $dbfName,
                $dbfPath . $dbfName);   
            }
        }
    
        protected function tearDown() : void
        {
            $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
            #$backupExtension = Config::getInstance()->get("BACKUP_EXTENSION");
            foreach(self::$databaseNames as $databaseName){  
                $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
                #if(fileExists($dbfPath . $dbfName . $backupExtension))
                #    unlink($dbfPath . $dbfName . $backupExtension);
                if(fileExists($dbfPath . $dbfName))
                    unlink($dbfPath . $dbfName);
                copy(self::DBS_UNTOUCHED_PATH . $dbfName,
                    $dbfPath . $dbfName);
            }
        }

        private function simulateChange($databaseName){
            $dbfPath = Config::getInstance()->get("DBF_FILES_PATH");
            $dbfName = Config::getInstance()->get("DBF_". strtoupper($databaseName) ."_NAME");
            copy(self::DBS_MODIFIED_PATH . $dbfName,
                $dbfPath . $dbfName);
        }
        
        public function testComparatorClientes()
        {
            $comparatorClientes = new Comparator(DatabaseNames::CLIENTES);
            $comparatorClientes->setCheckpoint();
            $this->simulateChange(DatabaseNames::CLIENTES);
            $comparatorClientes->checkDiferences();
            $newRecords = $comparatorClientes->getAcumulatedNewRecordsFound();
            $modifiedRecords = $comparatorClientes->getAcumulatedModifiedRecordsFound();
            $deletedRecords = $comparatorClientes->getAcumulatedDeletedRecordsFound();
            
            $this->assertEquals(count($newRecords), 1);
            $this->assertEquals(count($modifiedRecords), 1);
            $this->assertEquals(count($deletedRecords), 2);

            $this->assertEquals(
                $newRecords[0]->__toString(),
                "4252  ASD ASD ASD       CF    "
            );

            $this->assertEquals(
                $modifiedRecords[0]["from"]->__toString(),
                "4249 95204835 WLADER RUBEN ÑARI RAMIREZ RUTA 7 KM 18.5 JAUREGUI      CF    "
            );

            $this->assertEquals(
                $modifiedRecords[0]["to"]->__toString(),
                "4249 95204835 WLADER RUBEN NOANDALAENIE RUTA 7 KM 18.5 JAUREGUI      CF    "
            );

            $this->assertEquals(
                $deletedRecords[0]->__toString(),
                "4223 12677172 GUILLERMO ANTONIO ZURITA INT. GUILLERMON 141 GENERAL RODRIGUEZ      CF    "
            );

            $this->assertEquals(
                $deletedRecords[1]->__toString(),
                "2690 20471424 BIBIANA EDITH ZURLOSO RIVADAVIA 1698 GENERAL RODRIGUEZ 0     CF    "
            );
        }
    }
