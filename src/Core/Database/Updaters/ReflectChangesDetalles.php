<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Detail;
use API\Core\Database\Models\Brand;
use API\Core\Database\Models\Employee;
use API\Core\Database\Models\Model;
use API\Core\Database\Models\Job;
use API\Core\Database\Models\Person;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsDetalles;

use API\Core\Log;

class ReflectChangesDetalles
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsDetalles::$columns;
    }

    private function createTrabajo($record){

        $patente = $record->get("NUMBER_PLATE");

        $idMarca = Brand::obtenerOCrearMarca("")->ID_BRAND;

        $idModelo = Model::obtenerOCrearModelo("", $idMarca)->ID_MODEL;

        $idPersona = Person::obtenerExactoOSetearNuloPersona($record->get("NAME"), $record->get("SURNAME"))->ID_PERSON;

        $numeroTrabajo = $record->get("JOB_NUMBER");

        $descripcion = $record->get("DESCRIPTION");

        $idEmployee = Employee::firstOrCreate(["NAME" => "EmpleadoNulo"])->ID_EMPLOYEE;

        $trabajo = Job::createTrabajo($patente, $idModelo, $idPersona, $numeroTrabajo, $descripcion, $idEmployee);

        $return = null;

        if(!is_null($trabajo))
            $return = $trabajo;

        return $return;
    }

    private function isLoadedDetalle($record){
        $result = false;
        if(#strlen($record->get("NAME")) > 0 ||
            #strlen($record->get("SURNAME")) > 0 ||
            strlen($record->get("NUMBER_PLATE") > 0))
            $result = true;
        return $result;
    }

    private function obtenerIdTrabajo($record){
        $idTrabajo = null;

        /* BLOQUE PARA MEJORAR */
        if(strlen($record->get("JOB_NUMBER")) == 0){
            Log::alert("Detalle con JOB_NUMBER nulo", [$record]);
            //die;
        }
        /* */

        $trabajos = Job::where("NUMBER", $record->get("JOB_NUMBER"))->get();
        if(count($trabajos) == 1) /* El numero de trabajo no está duplicado en la DB */
            $idTrabajo = $trabajos[0]->ID_JOB;

        /* BLOQUE PARA MEJORAR */
        if(count($trabajos) == 0){ /* El detalle no pertenece a ningún trabajo */
            if($this->isLoadedDetalle($record))
            {
                $idTrabajo = $this->createTrabajo($record);
                if(!is_null($idTrabajo))
                    Log::alert("Detalle cuyo NRO_TRABAJO no se pudo encontrar en la db, se creo el trabajo.", [$record, $idTrabajo]);
                else
                    Log::alert("Detalle cuyo NRO_TRABAJO no se pudo encontrar en la db, se intentó crear el trabajo y falló.", [$record, $idTrabajo]);
                    
            } else{
                //Lo agrego al trabajo padre que almacena todos los detalles sin trabajo
                $trabajo = Job::firstOrCreate(
                    ["NUMBER" => 0],
                    ["DESCRIPTION" => "Trabajo para los detalles que no tienen un numero de trabajo",
                    "ID_EMPLOYEE" => Employee::firstOrCreate(["NAME" => "EmpleadoNulo"])->ID_EMPLOYEE]
                );
                $idTrabajo = $trabajo->ID_JOB;
                Log::alert("Detalle cuyo ID_JOB no se pudo encontrar en la db y no tenia datos para crear el trabajo, se asignó al trabajo nulo.", [$record, $record]);
            }
        }
        /* */

        if(count($trabajos) > 1){ /* El detalle pertenece a un trabajo que está duplicado */
            Log::alert("Detalle cuyo ID_JOB está duplicado en la db", [$record, $trabajos]);
            //die;
        }

        return $idTrabajo;
    }

    public function newRecords($records)
    { 
        foreach($records as $record)
        {
            try{
                #Log::Debug("Adding new record to database:", [$record]);

                $idTrabajo = $this->obtenerIdTrabajo($record);

                /* BLOQUE PARA MEJORAR */
                if(is_null($idTrabajo))
                    continue;
                /* */

                Detail::create([
                    "DESCRIPTION" => $record->get("DESCRIPTION"),
                    "AMOUNT" => $record->get("AMOUNT"),
                    "ID_JOB" => $idTrabajo,
                ]);

            }catch(Exception $e){
                Log::Error("ReflectChangesDetalles -> newRecords ->", [$e, $record]);     
                die;
            }
        }
    }

    /*
    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $detalle = Detail::where($record->getIndex())->first();
                $detalle->delete();
            } catch(Exception $e){
                Log::Error("ReflectChangesDetalles -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        Log::Error("ReflectChangesDetalles -> modifiedRecords -> This function was not prepared for use.");
       /* foreach($records as $record)
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
                Log::Error("ReflectChangesDetalle -> modifiedRecords ->", [$e, $record]);
            }
        }*/
   /* }*/
}