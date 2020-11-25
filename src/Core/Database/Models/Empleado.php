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

        $tipoEmpleado = TipoEmpleados::create(["PUESTO" => "Empleado"]);
    
        $empleado = self::firstOrCreate(
            ["NOMBRE" => $nombre],
            ["APELLIDO" => "SinApellido",
            "ID_TIPOEMPLEADO" => $tipoEmpleado->ID_TIPOEMPLEADO]
        );

        return $empleado->ID_EMPLEADO;
    }
}
