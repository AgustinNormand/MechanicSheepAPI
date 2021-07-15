<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Job;
use API\Core\Database\Models\Person;
use API\Core\Database\Models\Brand;
use API\Core\Database\Models\Empleado;
use API\Core\Database\Models\Employee;
use API\Core\Database\Models\Model;
use API\Core\Database\Models\Service;
use API\Core\Database\Models\Vehicle;
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

    public function newRecords($records)
    {    
        foreach($records as $record)
        {
            try{
                #Log::Debug("Adding new record to database:", [$record]);
 
                $patente = $record->get("NUMBER_PLATE");
                $idMarca = Brand::obtenerOCrearMarca($record->get("BRAND"))->ID_BRAND;
                $idModelo = Model::obtenerOCrearModelo($record->get("MODEL"), $idMarca)->ID_MODEL;
                $vehiculo = Vehicle::obtenerVehiculo($patente, $idModelo);
                if(is_null($vehiculo)){
                    $persona = Person::obtenerExactoOSetearNuloPersona($record->get("NAME"), $record->get("SURNAME"));
                    $vehiculo = Vehicle::crearVehiculo($patente, $persona->ID_PERSON, $idModelo);
                }

                $servicio = Service::obtenerOSetearNuloServicio($record->get("DESCRIPTION"));

                $numeroTrabajo = $record->get("NUMBER");
                if(strlen($numeroTrabajo) == 0)
                    $numeroTrabajo = null;

                $trabajo = Job::create([
                    "NUMBER" => $numeroTrabajo, 
                    "DATE" => $record->get("DATE"),
                    "KILOMETERS" => $record->get("KILOMETERS"),
                    "ID_SERVICE" => $servicio->ID_SERVICE,
                    "ID_VEHICLE" => $vehiculo->ID_VEHICLE,
                    "ID_EMPLOYEE" => Employee::firstOrCreate(["NAME" => $record->get("EMPLOYEE")])->ID_EMPLOYEE,
                ]);

            }catch(Exception $e){
                Log::Error("ReflectChangesTrabajos -> newRecords ->", [$e, $record]);
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
                $trabajo = Job::where("NRO_TRABAJO", $record->get("NRO_TRABAJO"))->first();
                $trabajo->delete();
            } catch(Exception $e){
                Log::Error("ReflectChangesTrabajos -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        Log::Error("ReflectChangesTrabajo -> modifiedRecords -> This function was not prepared for use.");
        /*foreach($records as $record)
        {
            try{
                Log::Debug("Modifing record in database:", [$record["from"], $record["to"]]);
                #if($record["from"]->getIndex() != $record["to"]->getIndex())
                #    Log::warning("ReflectChangesTrabajo -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $trabajo = Trabajo::find($record["from"]->getIndex());
                $trabajo->ID_TRABAJO = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $trabajo->$key = $record["to"]->get($key);
                }
                $trabajo->save();
            } catch(Exception $e){
                Log::Error("ReflectChangesTrabajo -> modifiedRecords ->", [$e, $record]);
            }
        }*/
    /*}*/
}