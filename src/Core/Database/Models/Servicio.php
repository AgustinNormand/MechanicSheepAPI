<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Servicio extends Eloquent
{
    protected $table = "servicios";
    protected $primaryKey = 'ID_SERVICIO';
    protected $guarded = [];

    public static function obtenerOSetearNuloServicio($descripcion){
        if(strlen($descripcion) == 0)
            $descripcion = "SinDescripcion";
        $servicio = self::firstOrCreate(["NOMBRE" => $descripcion]);
        return $servicio;
    }

}
