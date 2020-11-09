<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsClientes
{

    public static $columns = 
    [
        #NOMBRE ATRIBUTO DATABASE MYSQL => NOMBRE ATRIBUTO DATABASE DBF
        'DNI' => 'clidoc',
        'NOMBRE' => 'clinom',
        'APELLIDO' => 'cliape',
        'DIRECCION' => 'clidir',
        'LOCALIDAD' => 'cliloc',
        'CODIGO_POSTAL' => 'clicpo',
        'BARRIO' => 'clibar',
        'EMAIL' => 'climai',
        'TELEFONO' => 'clitel',

        /*Estos datos no sé si sirven*/
        'CUIT' => 'clicui',
        'CONDICION_IVA' => 'cliiva',
        'EMPRESA' => 'cliemp',
        'OTROS_DATOS' => 'cliobs',
        'CODIGO' => 'clicod',
        /**/
    ];

}