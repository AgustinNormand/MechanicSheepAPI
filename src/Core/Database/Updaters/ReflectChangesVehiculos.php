<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Vehicle;
use API\Core\Database\Models\Person;
use API\Core\Database\Models\Brand;
use API\Core\Database\Models\Model;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsVehiculos;
use API\Core\Log;

class ReflectChangesVehiculos
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsVehiculos::$columns;
    }

    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                #Log::Debug("Adding new record to database:", [$record]);

                $marca = Brand::obtenerOCrearMarca($record->get("BRAND"));

                $modelo = Model::obtenerOCrearModelo($record->get("MODEL"), $marca->ID_MARCA);

                $persona = Person::obtenerExactoOSetearNuloPersona($record->get("NAME"), $record->get("SURNAME"));

                $vehiculo = Vehicle::crearVehiculo($record->get("NUMBER_PLATE"),
                                                    $persona->ID_PERSON,
                                                    $modelo->ID_MODEL,
                                                    $record->get("VIN"),
                                                    $record->get("YEAR"),
                                                    $record->get("EGINE_NUMBER")
                                                    );

                if(is_null($vehiculo))
                    Log::Error("ReflectChangesVehiculos -> newRecords -> Vehiculo no creado", [$record]);
                        
            }catch(Exception $e){
                Log::Error("ReflectChangesVehiculos -> newRecords ->", [$e, $record]);     
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
                $vehiculo = Vehicle::where("PATENTE", $record->get("PATENTE"))->first();
                //Si hay mas de uno debería tomar una acicon diferente.
                $vehiculo->delete();
            } catch(Exception $e){
                Log::Error("ReflectChangesVehiculo -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Modifing record in database:", [$record["from"], $record["to"]]);
                #if($record["from"]->getIndex() != $record["to"]->getIndex())
                #    Log::warning("ReflectChangesVehiculo -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $vehiculo = Vehicle::where("PATENTE", $record["from"]->get("PATENTE"))->first();
                
                $marca = Brand::obtenerOCrearMarca($record["to"]->get("MARCA"));
                $modelo = Model::obtenerOCrearModelo($record["to"]->get("MODELO"), $marca->ID_MARCA);

                $vehiculo->PATENTE = $record["to"]->get("PATENTE");
                $vehiculo->VIN = $record["to"]->get("VIN");
                $vehiculo->ANIO = $record["to"]->get("ANIO");
                $vehiculo->NUMERO_MOTOR = $record["to"]->get("NUMERO_MOTOR");
                $vehiculo->ID_MODELO = $modelo->ID_MODELO;
                $vehiculo->save();
            } catch(Exception $e){
                Log::Error(" ReflectChangesVehiculo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }*/
}