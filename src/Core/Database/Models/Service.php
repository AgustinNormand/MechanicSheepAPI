<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Service extends Eloquent
{
    protected $table = "SERVICES";
    protected $primaryKey = 'ID_SERVICE';
    protected $guarded = [];

    public static function obtenerOSetearNuloServicio($descripcion){
        if(strlen($descripcion) == 0)
            $descripcion = "SinDescripcion";
        $servicio = self::firstOrCreate(["NAME" => $descripcion]);
        return $servicio;
    }

}
