<!DOCTYPE html>
<html>
    <head>
        <title>Lectura Databases</title>
    </head>
    <body>
        <form action="ReadDatabases.php" method="post">
        <p>
            Database: 
            <select name="DatabaseName">
                <option value="Clientes">Clientes</option>
                <option value="Trabajos">Trabajos</option>
                <option value="Vehiculos">Vehiculos</option>
                <option value="Detalles">Detalles</option>
            </select>
            <input type="submit" value="Submit">
        </p>
        </form>
    </body>
</html>