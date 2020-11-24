<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Empleado extends Eloquent
{
    protected $table = "empleados";
    protected $primaryKey = 'ID_EMPLEADO';
    protected $guarded = [];

    public static function obtenerExactoOSetearNulo($nombre){
        if(strlen($nombre) == 0)
            $nombre = "EmpleadoNulo";

        $idTipoEmpleado = TipoEmpleados::firstOrCreate(["PUESTO" => "Empleado"])->ID_TIPOEMPLEADO;
    
        $empleado = self::firstOrCreate(
            ["NOMBRE" => $nombre],
            ["APELLIDO" => "SinApellido",
            "ID_TIPOEMPLEADO" => $idTipoEmpleado]
        );

        return $empleado->ID_EMPLEADO;
    }
}
