<?php

namespace API\Core\Enum\DatabaseColumns;

final class DatabaseColumnsVehiculos
{

    public static $columns = 
    [
        'NUMBER_PLATE' => 'vehpat',
        'VIN' => 'vehvin',
        'YEAR' => 'vehano',
        'SURNAME' => 'vehape', #
        'NAME' => 'vehnom', #
        'BRAND' => 'vehmar', #FALTA EN LA BASE
        'MODEL' => 'vehmod',
        'ENGINE_NUMBER' => 'vehmot',
        #'CODIGO_STEREO' => 'vehste',
        #'CODIGO_LLAVE' => 'vehlla',
        #'CAB_LLAVE' => 'vehlla2', #?
        #'FECHA_COMPRA' => 'vehcom',      
    ];

}