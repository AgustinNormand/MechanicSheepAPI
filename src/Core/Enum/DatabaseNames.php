<?php

namespace API\Core\Enum;

class DatabaseNames
{
    const CLIENTES = 'Clientes';

    const VEHICULOS = 'Vehiculos';

    const DETALLES = 'Detalles';

    const TRABAJOS = 'Trabajos';

    static function all():array
    {
        return[
            self::CLIENTES,
            self::VEHICULOS,
            self::TRABAJOS,
            self::DETALLES,
        ];
    }
}