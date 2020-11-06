<?php 
    declare(strict_types=1);

    require __DIR__ . '/../src/bootstrap.php';

    use PHPUnit\Framework\TestCase;

    use API\Core\Comparators\ComparatorBase;
    use API\Core\Comparators\ComparatorTrabajos;
    use API\Core\Comparators\ComparatorDetalles;
    use API\Core\Comparators\ComparatorVehiculos;
    use API\Core\Comparators\ComparatorClientes;

    class ComparatorTest extends TestCase
    {
        private $path = __DIR__ . '/../DBS_FOR_TESTS/';

        private $dbNames = 
        [
            'Clientes' => 'climae.dbf',
            'Trabajos' => 'SERMAE.DBF',
            'Detalles' => 'sermae2.dbf',
            'Vehiculos' => 'VEHmae.DBF',
        ];

        private $cdxNames = 
        [
            'Clientes' => 'climae.CDX',
            'Trabajos' => 'sermae.cdx',
            'Detalles' => 'sermae2.CDX',
            'Vehiculos' => 'vehmae.CDX',
        ];

        private $limits = 
        [
            'Clientes' => [null, 1],
            'Trabajos' => [50, 1],
            'Detalles' => [50, 1],
            'Vehiculos' => [50, 1],
        ];

        

        private function prepareDirectory()
        {
            foreach($this->dbNames as $db)
            {
                try{
                    unlink($this->path . 'DBS_TEST_DIRECTORY/' . $db . '.bk');
                    unlink($this->path . 'DBS_TEST_DIRECTORY/' . $db);
                } catch (Exception $e){}
                copy($this->path . 'DBS_UNTOUCHED/' . $db, $this->path . 'DBS_TEST_DIRECTORY/' . $db);
            }
        }

        private function simulateChange($dbName)
        {
            $dbfFileName = $this->dbNames[$dbName];
            $cdxFileName = $this->cdxNames[$dbName];
            unlink($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            copy($this->path . "DBS_MODIFIED/{$dbfFileName}", $this->path . "DBS_TEST_DIRECTORY/$dbfFileName"); 
            copy($this->path . "DBS_MODIFIED/{$cdxFileName}", $this->path . "DBS_TEST_DIRECTORY/{$cdxFileName}");
        }

        public function testBackup()
        {
            $this->prepareDirectory();

            $comparator = new ComparatorBase;
            foreach($this->dbNames as $db)
            {
                $comparator->setCheckpoint($this->path . 'DBS_TEST_DIRECTORY/' . $db);
                $this->assertFileExists($this->path . 'DBS_TEST_DIRECTORY/' . $db . '.bk');
                unlink($this->path . 'DBS_TEST_DIRECTORY/' . $db . '.bk');
            }
        }

        /**
        * @depends testBackup
        */
        public function testNewClientes()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Clientes"];
            $comparator = new ComparatorClientes;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Clientes");
            $comparator->checkDiferences($this->limits["Clientes"][0], $this->limits["Clientes"][1]);
            $newRecords = $comparator->getAcumulatedNewRecordsFound();
            $newRecordsString = [];
            foreach($newRecords as $record)
                $newRecordsString[] = $comparator->toString($record);
           
            $this->assertEquals(
                $newRecordsString, 
                [
                    "ASD ASD   ASD        CF     S       ",
                ]);
        }

        public function testDeletedClientes()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Clientes"];
            $comparator = new ComparatorClientes;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Clientes");
            $comparator->checkDiferences($this->limits["Clientes"][0], $this->limits["Clientes"][1]);
            $deletedRecords = $comparator->getAcumulatedDeletedRecordsFound();
            $deletedRecordsString = [];
            foreach($deletedRecords as $record)
                $deletedRecordsString[] = $comparator->toString($record);
             
            $this->assertEquals(
                $deletedRecordsString, 
                [
                    "ZURITA GUILLERMO ANTONIO   INT. GUILLERMON 141 GENERAL RODRIGUEZ   12677172    CF     S       ",
                    "ZURLOSO BIBIANA EDITH   RIVADAVIA 1698 GENERAL RODRIGUEZ 0  20471424    CF     S       "
                ]);
        }

        public function testModifiedClientes()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Clientes"];
            $comparator = new ComparatorClientes;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Clientes");
            $comparator->checkDiferences($this->limits["Clientes"][0], $this->limits["Clientes"][1]);
            $modifiedRecords = $comparator->getAcumulatedModifiedRecordsFound();
            $modifiedRecordsFrom = [];
            $modifiedRecordsTo = [];
            foreach($modifiedRecords as $record){
                $modifiedRecordsFrom[] = $comparator->toString($record["from"]);
                $modifiedRecordsTo[] = $comparator->toString($record["to"]);
            }
             
            $this->assertEquals(
                $modifiedRecordsFrom, 
                [
                    "ÑARI RAMIREZ WLADER RUBEN   RUTA 7 KM 18.5 JAUREGUI   95204835    CF     S       ",
                ]);
            $this->assertEquals(
                $modifiedRecordsTo,
                [
                    "NOANDALAENIE WLADER RUBEN   RUTA 7 KM 18.5 JAUREGUI   95204835    CF     S       "
                ],
            );
        }

        public function testNewTrabajos()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Trabajos"];
            $comparator = new ComparatorTrabajos;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Trabajos");
            $comparator->checkDiferences($this->limits["Trabajos"][0], $this->limits["Trabajos"][1]);
            $newRecords = $comparator->getAcumulatedNewRecordsFound();
            $newRecordsString = [];
            foreach($newRecords as $record)
                $newRecordsString[] = $comparator->toString($record);
           
            $this->assertEquals(
                $newRecordsString, 
                [
                    "00014933 Tue, 03 Nov 2020 00:00:00 +0000 CKJ 830 FORD ESCORD GLX NORMAND JORGE ALBERTO E          ALTA DE REGISTRO         ERIKA   12345       S  ",
                ]);
        }

        public function testDeletedTrabajos()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Trabajos"];
            $comparator = new ComparatorTrabajos;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Trabajos");
            $comparator->checkDiferences($this->limits["Trabajos"][0], $this->limits["Trabajos"][1]);
            $deletedRecords = $comparator->getAcumulatedDeletedRecordsFound();
            $deletedRecordsString = [];
            foreach($deletedRecords as $record)
                $deletedRecordsString[] = $comparator->toString($record);
             
            $this->assertEquals(
                $deletedRecordsString, 
                [
                    "00014926 Sat, 12 Sep 2020 00:00:00 +0000 AD317LU RENAULT NUEVO LOGAN SAUCEDO FERNANDO DAVID E 4539.08 8110.92 12650 7264.08      SERVICE DE 10.000KM.         OVEJA   10130       S  "
                ]);
        }

        public function testModifiedTrabajos()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Trabajos"];
            $comparator = new ComparatorTrabajos;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Trabajos");
            $comparator->checkDiferences($this->limits["Trabajos"][0], $this->limits["Trabajos"][1]);
            $modifiedRecords = $comparator->getAcumulatedModifiedRecordsFound();
            $modifiedRecordsFrom = [];
            $modifiedRecordsTo = [];
            foreach($modifiedRecords as $record){
                $modifiedRecordsFrom[] = $comparator->toString($record["from"]);
                $modifiedRecordsTo[] = $comparator->toString($record["to"]);
            }
             
            $this->assertEquals(
                $modifiedRecordsFrom, 
                [
                    "00014925 Fri, 11 Sep 2020 00:00:00 +0000 AB537IE RENAULT KANGOO CONFORT 1.6 CUARENTA DANIELA E 16610.07 32716.61 68000 31869.77      MOTOR         OVEJA   52000       S  ",
                ]);
            $this->assertEquals(
                $modifiedRecordsTo,
                [
                    "00014925 Fri, 11 Sep 2020 00:00:00 +0000 AB537IE RENAULT KANGOO CONFORT 1.6 CUARENTA DANIELA E 36347.39 32716.61 69064 53596.18      MOTOR         OVEJA   52000       S  "
                ],
            );
        }
        
        public function testNewDetalles()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Detalles"];
            $comparator = new ComparatorDetalles;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Detalles");
            $comparator->checkDiferences($this->limits["Detalles"][0], $this->limits["Detalles"][1]);
            $newRecords = $comparator->getAcumulatedNewRecordsFound();
            $newRecordsString = [];
            foreach($newRecords as $record)
                $newRecordsString[] = $comparator->toString($record);
           
            $this->assertEquals(
                $newRecordsString, 
                [
                    "S 2 MOTOR 1 32716.61 32716.61 0 00014925     N 0 CUARENTA DANIELA AB537IE     ",
                    "S 8200108203 FILTRO DE ACEITE MOT.K4M 1 494.08 494.08 345.86 00014925     N 0        ",
                    "S 8200431051 I RX 0225241229 1 1029.06 1029.06 720.34 00014925     N 0        ",
                    "S 555 TORNILLOS DE TAPA 1 1700 1700 39.93 00014925     N 0        ",
                    "S 115 CORREA 1 1100 1100 36.3 00014925     N 0        ",
                    "S 3551 REPARAR TAPA DE CILINDRO KANGOO 1 15500 15500 526.35 00014925     N 0        ",
                    "S 7700500155 I BUJIA CHAMPION RC87YCL 4 314.6 1258.4 251.68 00014925     N 0        ",
                    "S 130C13191R I COLECC.DISTRIBUCION MOTOR 1 12345.85 12345.85 9025.48 00014925     N 0        ",
                    "S 0225241934 COMPETITION 10W40 4L. 1 2920 2920 3766.84 00014925     N 0        ",

                ]);
        }

        public function testDeletedDetalles()
        {
            $this->prepareDirectory();

            $dbfFileName = $this->dbNames["Detalles"];
            $comparator = new ComparatorDetalles;
            $comparator->setCheckpoint($this->path . "DBS_TEST_DIRECTORY/{$dbfFileName}");
            $this->simulateChange("Detalles");
            $comparator->checkDiferences($this->limits["Detalles"][0], $this->limits["Detalles"][1]);
            $deletedRecords = $comparator->getAcumulatedDeletedRecordsFound();
            $deletedRecordsString = [];
            foreach($deletedRecords as $record)
                $deletedRecordsString[] = $comparator->toString($record);
             
            $this->assertEquals(
                $deletedRecordsString, 
                [
                    "S 0225241934 COMPETITION 10W40 4L. 1 2920 2920 3766.84 00014925     N 0        ",
                    "S 130C13191R I COLECC.DISTRIBUCION MOTOR 1 11281.85 11281.85 9025.48 00014925     N 0        ",
                    "S 7700500155 I BUJIA CHAMPION RC87YCL 4 314.6 1258.4 251.68 00014925     N 0        ",
                    "S 3551 REPARAR TAPA DE CILINDRO KANGOO 1 15500 15500 526.35 00014925     N 0        ",
                    "S 115 CORREA 1 1100 1100 36.3 00014925     N 0        ",
                    "S 555 TORNILLOS DE TAPA 1 1700 1700 39.93 00014925     N 0        ",
                    "S 8200431051 I RX 0225241229 1 1029.06 1029.06 720.34 00014925     N 0        ",
                    "S 8200108203 FILTRO DE ACEITE MOT.K4M 1 494.08 494.08 345.86 00014925     N 0        ",
                    "S 2 MOTOR 1 32716.61 32716.61 0 00014925     N 0 CUARENTA DANIELA AB537IE     ",
                ]);
        }
        
    }
