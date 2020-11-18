<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsTrabajos
{

    public static $columns = 
    [
        'ID_TRABAJO' => 'sernro',
        'FECHA' => 'serfec',
        'DESCRIPCION' => 'seracu1',
        'KILOMETROS' => 'serklm',
        'PATENTE' => 'serpat', #IDVEHICULO
        'MODELO' => 'sermod', #
        'APELLIDO' => 'serape', #
        'NOMBRE' => 'sernom', #
        'ESTADO' => 'serest', #NO ESTA RELACIONADO CON TRABAJO
        'EMPLEADO' => 'sertec', #NO ESTA EN LA BASE
        'FECHA_FINALIZACION' => 'serter', #NO ESTA EN LA BASE
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