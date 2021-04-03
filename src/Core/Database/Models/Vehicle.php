<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Vehicle extends Eloquent
{
    protected $table = "VEHICLES";
    protected $primaryKey = 'ID_VEHICLE';
    protected $guarded = [];

    public static function obtenerVehiculo($patente, $idModelo){

        $result = null;

        $vehiculos = self::where([
            ["NUMBER_PLATE", $patente],
            ["ID_MODEL", $idModelo]
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
        
        $anio = $record->get("YEAR");
        if(strlen($anio) > 0)
            $vehiculo->YEAR = $anio;
        
        $numeroMotor = $record->get("ENGINE_NUMBER");
        if(strlen($numeroMotor) > 0)
            $vehiculo->ENGINE_NUMBER = $numeroMotor;

        $vehiculo->ID_PERSON = $idPersona; //Si la persona es nula, la voy a remplazar con la actual, que capaz es valida
        $vehiculo->save();
    }

    public static function crearVehiculo($patente, $idPersona, $idModelo, $vin = null, $anio = null, $numeroMotor = null){
        $result = null;

        $vehiculos = self::where("NUMBER_PLATE", $patente)->get();
        //Tengo que ordenarlos del mas nuevo al mas viejo

        ///Si vehiculos es ==1 o >1, hay uno o más vehiculos con la misma patente
        if(count($vehiculos) > 1)
        {
            /* 
            Estaría bueno acá hacer una logica como la de git con los conflictos, 
            preguntarle a un moderador que desición quiere que tomar.
            */

            $found = false;

            foreach($vehiculos as $vehiculo){
                if(($vehiculo->ID_MODEL == $idModelo) and $found == true)
                    $vehiculo->delete();
                else
                    if (($vehiculo->ID_MODEL == $idModelo) and $found == false)
                    {
                        $found = true;

                        $vehiculo->ID_PERSON = $idPersona;
                        if(strlen($vin) > strlen($vehiculo->VIN))
                            $vehiculo->VIN = $vin;
                        if(strlen($anio) > strlen($vehiculo->YEAR))
                            $vehiculo->YEAR = $anio;
                        if(strlen($numeroMotor) > strlen($vehiculo->ENGINE_NUMBER))
                            $vehiculo->ENGINE_NUMBER = $numeroMotor;
                        $vehiculo->save();

                        $result = $vehiculo;
                    }
            }

            if($found == false){ //No encontró un vehiculo que tenga el mismo modelo del que se está ingresando
                $result = Vehicle::create([
                    "NUMBER_PLATE" => $patente,
                    "ID_MODEL" => $idModelo,
                    "VIN" => $vin,
                    "YEAR" => $anio,
                    "ENGINE_NUMBER" => $numeroMotor,
                    "ID_PERSON" => $idPersona,
                ]);
            }
        }

        if(count($vehiculos) == 1)
        {
            $vehiculos[0]->ID_MODEL = $idModelo;
            $vehiculos[0]->VIN = $vin;
            $vehiculos[0]->YEAR = $anio;
            $vehiculos[0]->ENGINE_NUMBER = $numeroMotor;
            $vehiculos[0]->ID_PERSON = $idPersona;
            $vehiculos[0]->save();
            $result = $vehiculos[0];
        }

        if(count($vehiculos) == 0) {
            $result = Vehicle::create([
                "NUMBER_PLATE" => $patente,
                "ID_MODEL" => $idModelo,
                "VIN" => $vin,
                "YEAR" => $anio,
                "ENGINE_NUMBER" => $numeroMotor,
                "ID_PERSON" => $idPersona,
            ]);
            
        }
        return $result;
    }
}
