<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Vehiculo extends Eloquent
{
    protected $table = "vehiculos";
    protected $primaryKey = 'ID_VEHICULO';
    protected $guarded = [];

    public static function obtenerVehiculo($patente, $idModelo){

        $result = null;

        $vehiculos = self::where([
            ["PATENTE", $patente],
            ["ID_MODELO", $idModelo]
        ])->get();

        if(count($vehiculos) == 1)
            $result = $vehiculos[0];
        

        return $result;
        //if(count($vehiculo) == 0)
            //$vehiculo = self::crearVehiculo($idModelo, $idPersona, $record);
            //else
                //if(count($vehiculo) == 1)
                    //$vehiculo = self::actualizarVehiculo($vehiculo[0], $idPersona, $record);
                //else
                    //Log::Error("ReflectChangesVehiculos -> newRecords -> El select de vehiculo a la db, dio más de 1", [$record]);
    }

    public static function actualizarVehiculo($vehiculo, $idPersona, $record){
        $vin = $record->get("VIN");
        if(strlen($vin) > 0)            
            $vehiculo->VIN = $vin;
        
        $anio = $record->get("ANIO");
        if(strlen($anio) > 0)
            $vehiculo->ANIO = $anio;
        
        $numeroMotor = $record->get("NUMERO_MOTOR");
        if(strlen($numeroMotor) > 0)
            $vehiculo->NUMERO_MOTOR = $numeroMotor;

        $vehiculo->ID_PERSONA = $idPersona; //Si la persona es nula, la voy a remplazar con la actual, que capaz es valida
        $vehiculo->save();
    }

    public static function crearVehiculo($patente, $idPersona, $idModelo, $vin = null, $anio = null, $numeroMotor = null){
        $result = null;

        $vehiculos = self::where("PATENTE", $patente)->get();
        //Tengo que ordenarlos del mas nuevo al mas viejo

        ///Si vehiculos es ==1 o >1, hay uno o más vehiculos con la misma patente
        if(count($vehiculos) >= 1)
        {
            /* 
            Estaría bueno acá hacer una logica como la de git con los conflictos, 
            preguntarle a un moderador que desición quiere que tomar.
            */

            $found = false;

            foreach($vehiculos as $vehiculo){
                if(($vehiculo->ID_MODELO == $idModelo) and $found == true)
                    $vehiculo->delete();
                else
                    if (($vehiculo->ID_MODELO == $idModelo) and $found == false)
                    {
                        $found = true;

                        $vehiculo->ID_PERSONA = $idPersona;
                        if(strlen($vin) > strlen($vehiculo->VIN))
                            $vehiculo->VIN = $vin;
                        if(strlen($anio) > strlen($vehiculo->ANIO))
                            $vehiculo->ANIO = $anio;
                        if(strlen($numeroMotor) > strlen($vehiculo->NUMERO_MOTOR))
                            $vehiculo->NUMERO_MOTOR = $numeroMotor;
                        $vehiculo->save();

                        $result = $vehiculo;
                    }
            }

            if($found == false){ //No encontró un vehiculo que tenga el mismo modelo del que se está ingresando
                $result = Vehiculo::create([
                    "PATENTE" => $patente,
                    "ID_MODELO" => $idModelo,
                    "VIN" => $vin,
                    "ANIO" => $anio,
                    "NUMERO_MOTOR" => $numeroMotor,
                    "ID_PERSONA" => $idPersona,
                ]);
            }
        }

        if(count($vehiculos) == 0) {
            $result = Vehiculo::create([
                "PATENTE" => $patente,
                "ID_MODELO" => $idModelo,
                "VIN" => $vin,
                "ANIO" => $anio,
                "NUMERO_MOTOR" => $numeroMotor,
                "ID_PERSONA" => $idPersona,
            ]);
            
        }
        return $result;
    }
}
