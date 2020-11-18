<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsVehiculos
{

    public static $columns = 
    [
        'PATENTE' => 'vehpat',
        'VIN' => 'vehvin',
        'ANIO' => 'vehano',
        'APELLIDO' => 'vehape', #
        'NOMBRE' => 'vehnom', #
        'MARCA' => 'vehmar', #FALTA EN LA BASE
        'MODELO' => 'vehmod',
        'NUMERO_MOTOR' => 'vehmot',
        #'CODIGO_STEREO' => 'vehste',
        #'CODIGO_LLAVE' => 'vehlla',
        #'CAB_LLAVE' => 'vehlla2', #?
        #'FECHA_COMPRA' => 'vehcom',      
    ];

}