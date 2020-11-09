<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsVehiculos
{

    public static $columns = 
    [
        'PATENTE' => 'vehpat',
        'APELLIDO' => 'vehape',
        'NOMBRE' => 'vehnom',
        'MARCA' => 'vehmod',
        'ANO' => 'vehano',
        'NUMERO_MOTOR' => 'vehmot',
        'VIN' => 'vehvin',

        'CODIGO_STEREO' => 'vehste',
        'CODIGO_LLAVE' => 'vehlla',
        'CAB_LLAVE' => 'vehlla2', #?
        'FECHA_COMPRA' => 'vehcom',      
    ];

}