<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;


class Employee extends Eloquent
{
    protected $table = "EMPLOYEES";
    protected $primaryKey = 'ID_EMPLOYEE';
    protected $guarded = [];

    public static function obtenerExactoOSetearNulo($nombre){
        if(strlen($nombre) == 0)
            $nombre = "EmpleadoNulo";

        $tipoEmpleado = TipoEmpleados::firstOrcreate(["PUESTO" => "Empleado"]); //Creo que eso arregla que cree miles de tipoempleado
    
        $empleado = self::firstOrCreate(
            ["NOMBRE" => $nombre],
            ["APELLIDO" => "SinApellido",
            "ID_TIPOEMPLEADO" => $tipoEmpleado->ID_TIPOEMPLEADO]
        );

        return $empleado->ID_EMPLEADO;
    }
}
