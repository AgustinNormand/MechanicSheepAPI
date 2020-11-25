<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Vehiculo;
use API\Core\Database\Models\Cliente;
use API\Core\Database\Models\Marca;
use API\Core\Database\Models\Modelo;
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
                Log::Debug("Adding new record to database:", [$record]);

                $marca = Marca::obtenerOCrearMarca($record->get("MARCA"));

                $modelo = Modelo::obtenerOCrearModelo($record->get("MODELO"), $marca->ID_MARCA);

                $persona = Cliente::obtenerExactoOSetearNuloPersona($record->get("NOMBRE"), $record->get("APELLIDO"));

                $vehiculo = Vehiculo::crearVehiculo($record->get("PATENTE"),
                                                    $persona->ID_PERSONA,
                                                    $modelo->ID_MODELO,
                                                    $record->get("VIN"),
                                                    $record->get("ANIO"),
                                                    $record->get("NUMERO_MOTOR")
                                                    );

                if(is_null($vehiculo))
                    Log::Error("ReflectChangesVehiculos -> newRecords -> Vehiculo no creado", [$record]);
                        
            }catch(Exception $e){
                Log::Error("ReflectChangesVehiculos -> newRecords ->", [$e, $record]);     
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
                $vehiculo = Vehiculo::find($record->getIndex());
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
                if($record["from"]->getIndex() != $record["to"]->getIndex())
                    Log::warning("ReflectChangesVehiculo -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $vehiculo = Vehiculo::find($record["from"]->getIndex());
                $vehiculo->ID_VEHICULO = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $vehiculo->$key = $record["to"]->get($key);
                }
                $vehiculo->save();
            } catch(Exception $e){
                Log::Error(" ReflectChangesVehiculo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}