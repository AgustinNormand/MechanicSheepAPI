<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use API\Core\Database\Models\Vehiculo;

class Job extends Eloquent
{
    protected $table = "JOBS";
    protected $primaryKey = 'ID_JOB';
    protected $guarded = [];

    public static function createTrabajo($patente, $idModelo, $idPersona, $numeroTrabajo, $descripcion, $idEmployee){
        $return = null;
        $vehiculo = null;
        $vehiculos = Vehicle::where("NUMBER_PLATE", $patente)->get();
        //Si hay mas de 1?
        if(count($vehiculos) == 0){
            $vehiculo = Vehicle::crearVehiculo($patente, $idPersona, $idModelo);

        }

        if(count($vehiculos) == 1)
            $vehiculo = $vehiculos[0];
            //No necesito dar de alta el vehiculo
            //Podría ser que me de cuenta aca que el vehiculo cambio de dueño con nombre y apellido cliente.

        if(count($vehiculos) > 1){
            //No puedo identificar a que vehiculo se realizó el trabajo.
            //Acá habria que preguntarle a un moderador.
        }

        if(!is_null($vehiculo)){
            if(strlen($numeroTrabajo) == 0)
                $numeroTrabajo = null;

            $servicio = Service::obtenerOSetearNuloServicio($descripcion);

            $trabajo = Job::create([
                "NUMBER" => $numeroTrabajo,
                "ID_SERVICE" => $servicio->ID_SERVICIO,
                "ID_VEHICLE" => $vehiculo->ID_VEHICULO,
                "ID_EMPLOYEE" => $idEmployee,
            ]);

            $return = $trabajo->ID_TRABAJO;
        }

        return $return;
    }
}
