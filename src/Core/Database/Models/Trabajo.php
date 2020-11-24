<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use API\Core\Database\Models\Vehiculo;

class Trabajo extends Eloquent
{
    protected $table = "trabajos";
    protected $primaryKey = 'ID_TRABAJO';
    protected $guarded = [];

    public static function createTrabajo($patente, $idModelo, $idPersona, $numeroTrabajo, $descripcion){
        $return = null;
        $vehiculo = null;
        $vehiculos = Vehiculo::where("PATENTE", $patente)->get();
        //Si hay mas de 1?
        if(count($vehiculos) == 0){
            $vehiculo = Vehiculo::crearVehiculo($patente, $idPersona, $idModelo);

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

            $servicio = Servicio::obtenerOSetearNuloServicio($descripcion);

            $trabajo = Trabajo::create([
                "NRO_TRABAJO" => $numeroTrabajo,
                "ID_SERVICIO" => $servicio->ID_SERVICIO,
                "ID_VEHICULO" => $vehiculo->ID_VEHICULO
            ]);

            $return = $trabajo->ID_TRABAJO;
        }

        return $return;
    }
}
