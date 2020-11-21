<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Trabajo;
use API\Core\Database\Models\Marca;
use API\Core\Database\Models\Modelo;
use API\Core\Database\Models\Servicio;
use API\Core\Database\Models\Vehiculo;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsTrabajos;
use API\Core\Database\Updaters\ReflectChangesVehiculos;
use API\Core\Log;

use function PHPUnit\Framework\isNull;

class ReflectChangesTrabajos
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsTrabajos::$columns;
    }

    /* Copiado del otro ReflectChanges, hay que refactorizar esto*/

    private function obtenerOCrearMarca($marca){
        if(strlen($marca) == 0)
            $marca = "SinMarca";

        $marca = Marca::firstOrCreate(
            ['RAZON_SOCIAL' => $marca]
        );
        return $marca;
    }

    private function obtenerOCrearModelo($modelo, $marca){
        if(strlen($modelo) == 0)
            $modelo = "SinModelo";
            $modelo = Modelo::firstOrCreate(
            ['NOMBRE_FANTASIA' => $modelo],
            ['ID_MARCA' => $marca->ID_MARCA]
        );
        return $modelo;
    }

    /* */

    public function obtenerOSetearNuloServicio($descripcion){
        if(strlen($descripcion) == 0)
            $descripcion = "SinDescripcion";
        $servicio = Servicio::firstOrCreate(["NOMBRE" => $descripcion]);
        return $servicio;
    }

    private function crearVehiculo($record){
        $reflectChangesVehiculo = new ReflectChangesVehiculos;

        $marca = $reflectChangesVehiculo->obtenerOCrearMarca($record->get("MARCA"));

        $modelo = $reflectChangesVehiculo->obtenerOCrearModelo($record->get("MODELO"), $marca);

        $persona = $reflectChangesVehiculo->obtenerOSetearNuloPersona($record->get("NOMBRE"), $record->get("APELLIDO"));

        $vehiculo = Vehiculo::create([
            "PATENTE" => $record->get("PATENTE"), 
            "ID_MODELO" => $modelo->ID_MODELO,
            "ID_PERSONA" => $persona->ID_PERSONA
        ]);

        return $vehiculo;

    }
    
    private function obtenerVehiculo($record){
        $patenteAuto = $record->get("PATENTE");
        $marca = $this->obtenerOCrearMarca($record->get("MARCA"));
        $modelo = $this->obtenerOCrearModelo($record->get("MODELO"), $marca);
        $vehiculo = Vehiculo::where([
            ["PATENTE", $patenteAuto],
            ["ID_MODELO", $modelo->ID_MODELO]
            ])->get();
            
        if(count($vehiculo) == 0){
            Log::error("Error in ReflectChangesTrabajos -> newRecords -> El select de vehiculo a la db, dio 0. Hay un trabajo realizado a un vehiculo no registrado.", [$record]);
            $return = $this->crearVehiculo($record);
            
        } else
            $return = $vehiculo[0];

        if(count($vehiculo) > 1){
            Log::error("Error in ReflectChangesTrabajos -> newRecords -> El select de vehiculo a la db, dio más que 1. Hay un trabajo realizado a un vehiculo que está duplicado ." , [$record]);
        }
        return $return;
    }
    /*
    private function getAndSetIfDuplicated($numeroTrabajo){
        $duplicated = 0;
        if(!is_null($numeroTrabajo))
        {
            $trabajosDuplicados = Trabajo::where(
                "NRO_TRABAJO", $numeroTrabajo
                )->get();
                if(count($trabajosDuplicados)>=1){
                    $duplicated = 1;
                    foreach($trabajosDuplicados as $trabajoDuplicado)
                    {
                        $trabajoDuplicado->DUPLICATED = 1;
                        $trabajoDuplicado->save();
                    }
                }
        }
        return $duplicated;
    }
    */
    public function newRecords($records)
    {    
        foreach($records as $record)
        {
            try{
                //$record->getIndex();
                Log::Debug("Adding new record to database:", [$record]);
                //var_dump($record);
                //die;
                $vehiculo = $this->obtenerVehiculo($record);
                //if(is_null($vehiculo)){

                //}
                    //continue;

                $servicio = $this->obtenerOSetearNuloServicio($record->get("DESCRIPCION"));

                $numeroTrabajo = $record->get("NRO_TRABAJO");
                if(strlen($numeroTrabajo) == 0)
                    $numeroTrabajo = null;

                //$duplicated = $this->getAndSetIfDuplicated($numeroTrabajo);

                Trabajo::create([
                    "NRO_TRABAJO" => $numeroTrabajo, 
                    "FECHA" => $record->get("FECHA"),
                    "KILOMETROS" => $record->get("KILOMETROS"),
                    "ID_SERVICIO" => $servicio->ID_SERVICIO,
                    "ID_VEHICULO" => $vehiculo->ID_VEHICULO,
                    //"DUPLICATED" => $duplicated,
                ]);
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesTrabajos -> newRecords ->", [$e, $record]);
                die;
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $trabajo = Trabajo::find($record->getIndex());
                $trabajo->delete();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesTrabajos -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Modifing record in database:", [$record["from"], $record["to"]]);
                if($record["from"]->getIndex() != $record["to"]->getIndex())
                    Log::warning("ReflectChangesTrabajo -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $trabajo = Trabajo::find($record["from"]->getIndex());
                $trabajo->ID_TRABAJO = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $trabajo->$key = $record["to"]->get($key);
                }
                $trabajo->save();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesTrabajo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}