<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsTrabajos
{

    public static $columns = 
    [
        'NUMERO' => 'sernro',
        'FECHA' => 'serfec',
        'PATENTE' => 'serpat',
        'MODELO' => 'sermod',
        'APELLIDO' => 'serape',
        'NOMBRE' => 'sernom',
        'ESTADO' => 'serest',
        'DESCRIPCION' => 'seracu1',
        'EMPLEADO' => 'sertec',
        'KILOMETROS' => 'serklm',
        'FECHA_FINALIZACION' => 'serter',
        #'COSTO_REPUESTOS' => 'sercosr',
        #'COSTO_MANO_OBRA' => 'sercosm',
        #'TOTAL' => 'sertot',
        #'GANANCIA' => 'sercosg',
        #'FORMA_PAGO' => 'movcta',
        #'COMPROBANTE' => 'movcom',
        #'TIPO_FACTURA' => 'movtip',
        'NRO_SUCURSAL' => 'movsuc',
        'NRO_MOVIMIENTO' => 'movnro',
    ];

}