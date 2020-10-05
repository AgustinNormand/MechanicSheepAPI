<!DOCTYPE html>
<html>
    <head>
        <title>Lectura Databases</title>
    </head>
    <body>

        <p>Base de datos de 
        <?php $databaseName = strtoupper($_POST["DatabaseName"]); 
        echo $databaseName;?>
        </p>

        <?php
        /*
        session_start() 
        $_SESSION['database'] = $_POST['DatabaseName']; 
        */

        require_once('Gestor.php');

        $gestor = new Gestor($databaseName);
        
        echo '<p>';
        echo 'Cantidad de registros: ' . $gestor->getCantidadRegistros();
        echo '</p>';

        echo '<p>';
        echo 'Columnas reales: ';
        echo $gestor->getColumnasReales();
        echo '</p>';

        echo '<p>';
        echo 'Columnas filtradas: ';
        echo $gestor->getColumnasFiltradas();
        echo '</p>';

        ?>

        <form action="ReadDatabases.php" method="post">
        <input type="hidden" name="DatabaseName" value=<?php echo $databaseName ?>><br>
        ColumnaFiltrada: <input type="text" name="columna"><br>
        Valor: <input type="text" name="valor"><br>
        <input type="submit" value="Search">
        </form>


        <?php

        $databaseName = strtoupper($_POST["DatabaseName"]);
        $columna = strtolower($_POST['columna']);
        $valor = strtoupper($_POST['valor']);

        echo '<p>';
        echo 'Resultado del search:';
        echo '<br>';
        $result = $gestor->search($columna, $valor);        
        $gestor->printData($result);
        echo '</p>';
        ?>

    </body>
</html>