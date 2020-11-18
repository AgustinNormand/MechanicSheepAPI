<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsClientes
{

    public static $columns = 
    [
        #NOMBRE ATRIBUTO DATABASE MYSQL => NOMBRE ATRIBUTO DATABASE DBF
        'NOMBRE' => 'clinom',
        'APELLIDO' => 'cliape',
        'NRO_DOC' => 'clidoc',
        #FECHA_NAC NO TENGO
        'CALLE' => 'clidir',
        #NRO_CALLE HAY QUE PARSEARLO
        'LOCALIDAD' => 'cliloc',
        #PAIS NO TENGO
        'TELEFONO' => 'clitel',
        'EMAIL' => 'climai',
        #DESCRIPCION NO TENGO
        'CODIGO_POSTAL' => 'clicpo',
        #'BARRIO' => 'clibar', NO ESTÁ EN LA BASE
        
        /*Estos datos no sé si sirven*/
        #'CUIT' => 'clicui',
        #'CONDICION_IVA' => 'cliiva',
        #'EMPRESA' => 'cliemp',
        #'OTROS_DATOS' => 'cliobs',
        #'CODIGO' => 'clicod',
        /**/
    ];

}