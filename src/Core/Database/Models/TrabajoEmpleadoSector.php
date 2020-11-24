<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use API\Core\Database\Models\Empleado;

class TrabajoEmpleadoSector extends Eloquent
{
    protected $table = "trabajo_empleado_sector";
    protected $guarded = [];

    public static function asignarTrabajoAEmpleadoASector($nombreEmpleado, $idTrabajo, $nroSector){
        if(strlen($nroSector) == 0)
            $nroSector = "001";

        $idTaller = Taller::firstOrCreate(["NOMBRE" => "MechanicSheep-SedeLujan"], ["ESTADO" => 1])->ID_TALLER;
        $idSector = Sector::firstOrCreate(["ID_SECTOR" => $nroSector], ["NOMBRE" => "SectorPrincipal", "ID_TALLER" => $idTaller, "ESTADO" => 1])->ID_SECTOR;
        $idEmpleado = Empleado::obtenerExactoOSetearNulo($nombreEmpleado, $idSector);
        SectorEmpleado::firstOrCreate(["ID_SECTOR" => $idSector, "ID_EMPLEADO" => $idEmpleado, "ID_TALLER" => $idTaller]);
        TrabajoEmpleado::asignarTrabajoAEmpleado($idEmpleado, $idTrabajo);
        return self::firstOrCreate(["ID_TRABAJO" => $idTrabajo, "ID_EMPLEADO" => $idEmpleado, "ID_SECTOR" => $idSector]);
    }
}
