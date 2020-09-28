<!DOCTYPE html>
<html>
    <head>
        <title>Lectura Databases</title>
    </head>
    <body>
        <p>Se listan a continuación las bases de datos</p>

        <?php  
        #phpinfo();
       # include 'dbase';

       echo 'Clientes: ';
       $db = dbase_open('/home/agustin/Desktop/Seminario/OldDatabases/climae.dbf', 2);
        if ($db) {
            echo $db;
            dbase_close($db);
            echo "<br>";
        } else
        {
            echo 'Error';
        }

        echo 'Detalle Trabajos: ';
        $db = dbase_open('/home/agustin/Desktop/Seminario/OldDatabases/sermae2.dbf', 2);
        if ($db) {          
            echo $db;
            dbase_close($db);
            echo "<br>";
        } else
        {
            echo 'Error';
        }

        echo 'Trabajos: ';
        $db = dbase_open('/home/agustin/Desktop/Seminario/OldDatabases/sermae.dbf', 2);
        if ($db) {
            echo $db;
            dbase_close($db);
            echo "<br>";
        } else
        {
            echo 'Error';
        }

        echo 'Vehiculos: ';
        $db = dbase_open('/home/agustin/Desktop/Seminario/OldDatabases/VEHmae.DBF', 2);
        if ($db) {
            echo $db;
            dbase_close($db);
            echo "<br>";
        } else
        {
            echo 'Error';
        }
        ?>

    </body>
</html>