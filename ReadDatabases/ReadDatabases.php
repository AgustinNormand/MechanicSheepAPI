<!DOCTYPE html>
<html>
    <head>
        <title>Lectura Databases</title>
    </head>
    <body>
        <p>Base de datos de VEHICULOS</p>

        <?php

        require "./vendor/autoload.php";

        use XBase\Table;

        $table = new Table('/home/agustin/Desktop/Seminario/Databases/vehmae.dbf');

        $records = $table->getRecordCount();
        $columns = $table->getColumns();
    
        echo '<p>';
        echo 'Cantidad de registros: ', $records;
        echo '</p>';

        echo '<p>';
        echo 'Columnas:';
        echo 'Patente, Apellido, Nombre, Marca, Modelo, AñoAuto, NumeroMotor, Vin, FechaCompra';
        echo '</p>';

        $patente = $columns['vehpat'];
        $apellido = $columns['vehape'];
        $nombre = $columns['vehnom'];
        $marca = $columns['vehmar'];
        $modelo = $columns['vehmod'];
        $año = $columns['vehano'];
        $numeroMotor = $columns['vehmot'];
        $vin = $columns['vehvin'];
        $fechaCompra = $columns['vehcom'];

        echo '<p>';

        while ($record = $table->nextRecord()) {
            echo $record->$patente;
            echo ', ';
            echo $record->$apellido;
            echo ', ';
            echo $record->$nombre;
            echo ', ';
            echo $record->$marca;
            echo ', ';
            echo $record->$modelo;
            echo ', ';
            echo $record->$año;
            echo ', ';
            echo $record->$numeroMotor;
            echo ', ';
            echo $record->$vin;
            echo ', ';
            echo $record->$fechaCompra;
            echo '<br>';

        }
        echo '</p>';
        ?>
        
    </body>
</html>