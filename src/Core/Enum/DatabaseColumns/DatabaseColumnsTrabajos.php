<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsTrabajos
{

    public static $columns = 
    [
        'NUMBER' => 'sernro',
        'DATE' => 'serfec',
        'DESCRIPTION' => 'seracu1',
        'KILOMETERS' => 'serklm',
        'NUMBER_PLATE' => 'serpat', #IDVEHICULO
        'MODEL' => 'sermod', #
        'BRAND' => 'sermar', #
        'SURNAME' => 'serape', #
        'NAME' => 'sernom', #
        'STATE' => 'serest', #NO ESTA RELACIONADO CON TRABAJO
        'EMPLOYEE' => 'sertec', #NO ESTA EN LA BASE
        #'FECHA_FINALIZACION' => 'serter', #NO ESTA EN LA BASE
        #'COSTO_REPUESTOS' => 'sercosr',
        #'COSTO_MANO_OBRA' => 'sercosm',
        #'TOTAL' => 'sertot',
        #'GANANCIA' => 'sercosg',
        #'FORMA_PAGO' => 'movcta',
        #'COMPROBANTE' => 'movcom',
        #'TIPO_FACTURA' => 'movtip',
        'NRO_SUCURSAL' => 'movsuc', # NO ESTA RELACIONADO CON TRABAJO
        'NRO_MOVIMIENTO' => 'movnro', # NO ESTA RELACIONADO CON TRABAJO
    ];

}