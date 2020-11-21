<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Detalle;
use API\Core\Database\Models\Marca;
use API\Core\Database\Models\Modelo;
use API\Core\Database\Models\Trabajo;
use API\Core\Database\Models\Vehiculo;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsDetalles;
use API\Core\Database\Updaters\ReflectChangesVehiculos;
use API\Core\Database\Updaters\ReflectChangesTrabajos;

use API\Core\Log;

class ReflectChangesDetalles
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsDetalles::$columns;
    }

    /* Habria que merjoarla, refactorizar y hacer más prolijo el codigo */
    private function createTrabajo($record){
        //echo "Detalle cargado" . PHP_EOL;
        //$patenteAuto = $record->get("PATENTE");
        $vehiculos = Vehiculo::where("PATENTE", $record->get("PATENTE"))->get();
        //Si hay mas de 1?
        if(count($vehiculos) == 0){
            //Doy de alta el vehiculo
            $reflectChangesVehiculo = new ReflectChangesVehiculos;

            $marca = $reflectChangesVehiculo->obtenerOCrearMarca("");

            $modelo = $reflectChangesVehiculo->obtenerOCrearModelo("", $marca);

            $persona = $reflectChangesVehiculo->obtenerOSetearNuloPersona($record->get("NOMBRE"), $record->get("APELLIDO_CLIENTE"));

            $vehiculo = Vehiculo::create([
                "PATENTE" => $record->get("PATENTE"), 
                "ID_MODELO" => $modelo->ID_MODELO,
                "ID_PERSONA" => $persona->ID_PERSONA
            ]);

            //echo "Hay un detalle, asignado a un vehiculo, que no está en el sistema" . PHP_EOL;
        }else
        {
            $vehiculo = $vehiculos[0];
            //No necesito dar de alta el vehiculo
            //Podría ser que me de cuenta aca que el vehiculo cambio de dueño con nombre y apellido cliente.
        }

        $numeroTrabajo = $record->get("NRO_TRABAJO");
                if(strlen($numeroTrabajo) == 0)
                    $numeroTrabajo = null;

        $reflectChangesTrabajo = new ReflectChangesTrabajos();

        $servicio = $reflectChangesTrabajo->obtenerOSetearNuloServicio($record->get("DESCRIPCION"));

        $trabajo = Trabajo::create([
            "NRO_TRABAJO" => $numeroTrabajo,
            "ID_SERVICIO" => $servicio->ID_SERVICIO,
            "ID_VEHICULO" => $vehiculo->ID_VEHICULO
        ]);
        return $trabajo->ID_TRABAJO;
    }
    /* Habria que merjoarla, refactorizar y hacer más prolijo el codigo */

    private function isLoadedDetalle($record){
        $result = false;
        if(strlen($record->get("NOMBRE_CLIENTE")) > 0 ||
            strlen($record->get("APELLIDO_CLIENTE")) > 0 ||
            strlen($record->get("PATENTE") > 0))
            $result = true;
        return $result;
    }

    private function obtenerIdTrabajo($record){
        $idTrabajo = null;

        /* BLOQUE PARA MEJORAR */
        if(strlen($record->get("NRO_TRABAJO")) == 0){
            Log::alert("Detalle con NRO_TRABAJO nulo", [$record]);
            //die;
        }
        /* */

        $trabajos = Trabajo::where("NRO_TRABAJO", $record->get("NRO_TRABAJO"))->get();
        if(count($trabajos) == 1) /* El numero de trabajo no está duplicado en la DB */
            $idTrabajo = $trabajos[0]->ID_TRABAJO;

        /* BLOQUE PARA MEJORAR */
        if(count($trabajos) == 0){ /* El detalle no pertenece a ningún trabajo */
            //LO CREO
            if($this->isLoadedDetalle($record))
            {
                //150
                $idTrabajo = $this->createTrabajo($record);
                Log::alert("Detalle cuyo NRO_TRABAJO no se pudo encontrar en la db, se creo el trabajo.", [$record, $idTrabajo]);
                //echo "En teoria creó el trabajo " . $idTrabajo . PHP_EOL;
            } else{
                //Lo agrego al trabajo padre que almacena todos los detalles sin trabajo
                $trabajo = Trabajo::firstOrCreate(
                    ["NRO_TRABAJO" => 0],
                    ["DESCRIPCION" => "Trabajo para los detalles que no tienen un numero de trabajo"]
                );
                $idTrabajo = $trabajo->ID_TRABAJO;
                Log::alert("Detalle cuyo NRO_TRABAJO no se pudo encontrar en la db y no tenia datos para crear el trabajo, se asignó al trabajo nulo.", [$record, $idTrabajo]);
            }
        }
        /* */

        if(count($trabajos) > 1){ /* El detalle pertenece a un trabajo que está duplicado */
            Log::alert("Detalle cuyo NRO_TRABAJO está duplicado en la db", [$record, $trabajos]);
            //die;
        }

        return $idTrabajo;
    }

    public function newRecords($records)
    { 
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);

                $idTrabajo = $this->obtenerIdTrabajo($record);

                /* BLOQUE PARA MEJORAR */
                if(is_null($idTrabajo))
                    continue;
                /* */

                Detalle::create([
                    "DESCRIPCION" => $record->get("DESCRIPCION"),
                    "CANTIDAD" => $record->get("CANTIDAD"),
                    "ID_TRABAJO" => $idTrabajo,
                ]);

            }catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalles -> newRecords ->", [$e, $record]);     
                #die;
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $detalle = Detalle::find($record->getIndex());
                $detalle->delete();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalles -> deletedRecords ->", [$e, $record]);
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
                    Log::warning("ReflectChangesDetalles -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $detalle = Detalle::find($record["from"]->getIndex());
                $detalle->ID_DETALLE = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $detalle->$key = $record["to"]->get($key);
                }
                $detalle->save();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalle -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}