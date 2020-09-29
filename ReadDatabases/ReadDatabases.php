<!DOCTYPE html>
<html>
    <head>
        <title>Lectura Databases</title>
    </head>
    <body>
        <?php

        require_once('Gestor.php');

        $databaseType = 'DETALLES'; #VALORES: CLIENTES, VEHICULOS, DETALLES, TRABAJOS.

        echo '<p>Base de datos de ' . $databaseType . '</p>';

        $gestor = new Gestor($databaseType);
        
        echo '<p>';
        echo 'Cantidad de registros: ' . $gestor->getCantidadRegistros();
        echo '</p>';

        echo '<p>';
        echo 'Columnas reales:';
        echo $gestor->getColumnasReales();
        echo '</p>';

        echo '<p>';
        echo 'Columnas filtradas:';
        echo $gestor->getColumnasFiltradas();
        echo '</p>';


        #echo '<p>';
        #echo 'Resultado del erase:';
        #echo '<br>';
        #$gestor->eraseSensibleInformation();
        #echo '</p>';


        echo '<p>';
        echo 'Resultado del search:';
        echo '<br>';
        $result = $gestor->search('numero', '00014925');        
        $gestor->printData($result);
        echo '</p>';
        ?>
    </body>
</html>