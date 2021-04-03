<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsClientes
{

    public static $columns = 
    [
        #NOMBRE ATRIBUTO DATABASE MYSQL => NOMBRE ATRIBUTO DATABASE DBF
        'NAME' => 'clinom',
        'SURNAME' => 'cliape',
        'DOCUMENT_NUMBER' => 'clidoc',
        #FECHA_NAC NO TENGO
        'STREET' => 'clidir',
        #NRO_CALLE HAY QUE PARSEARLO
        'LOCALITY' => 'cliloc',
        #PAIS NO TENGO
        'PHONE' => 'clitel',
        'EMAIL' => 'climai',
        #DESCRIPCION NO TENGO
        'POSTAL_CODE' => 'clicpo',
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