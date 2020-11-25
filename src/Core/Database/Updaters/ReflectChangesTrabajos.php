<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Trabajo;
use API\Core\Database\Models\Cliente;
use API\Core\Database\Models\Marca;
use API\Core\Database\Models\Modelo;
use API\Core\Database\Models\Servicio;
use API\Core\Database\Models\TrabajoEmpleadoSector;
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

    public function newRecords($records)
    {    
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);
 
                $patente = $record->get("PATENTE");
                $idMarca = Marca::obtenerOCrearMarca($record->get("MARCA"))->ID_MARCA;
                $idModelo = Modelo::obtenerOCrearModelo($record->get("MODELO"), $idMarca)->ID_MODELO;
                $vehiculo = Vehiculo::obtenerVehiculo($patente, $idModelo);
                if(is_null($vehiculo)){
                    $persona = Cliente::obtenerExactoOSetearNuloPersona($record->get("NOMBRE"), $record->get("APELLIDO"));
                    $vehiculo = Vehiculo::crearVehiculo($patente, $persona->ID_PERSONA, $idModelo);
                }

                $servicio = Servicio::obtenerOSetearNuloServicio($record->get("DESCRIPCION"));

                $numeroTrabajo = $record->get("NRO_TRABAJO");
                if(strlen($numeroTrabajo) == 0)
                    $numeroTrabajo = null;

                $trabajo = Trabajo::create([
                    "NRO_TRABAJO" => $numeroTrabajo, 
                    "FECHA" => $record->get("FECHA"),
                    "KILOMETROS" => $record->get("KILOMETROS"),
                    "ID_SERVICIO" => $servicio->ID_SERVICIO,
                    "ID_VEHICULO" => $vehiculo->ID_VEHICULO,
                ]);

                TrabajoEmpleadoSector::asignarTrabajoAEmpleadoASector($record->get("EMPLEADO"),
                                                                      $trabajo->ID_TRABAJO,
                                                                      $record->get("NRO_SUCURSAL"));
            }catch(Exception $e){
                Log::Error("ReflectChangesTrabajos -> newRecords ->", [$e, $record]);
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
                Log::Error("ReflectChangesTrabajos -> deletedRecords ->", [$e, $record]);
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
                Log::Error("ReflectChangesTrabajo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}