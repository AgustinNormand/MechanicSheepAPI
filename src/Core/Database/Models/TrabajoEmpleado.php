<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use API\Core\Database\Models\Empleado;

class TrabajoEmpleado extends Eloquent
{
    protected $table = "trabajo_empleado";
    protected $guarded = [];

    public static function asignarTrabajoAEmpleado($idEmpleado, $idTrabajo){
        return self::firstOrCreate(["ID_TRABAJO" => $idTrabajo], ["ID_EMPLEADO" => $idEmpleado]);
    }
}
