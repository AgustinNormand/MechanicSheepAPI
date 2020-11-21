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

    public function obtenerOCrearMarca($marca){
        if(strlen($marca) == 0)
            $marca = "SinMarca";

        $marca = Marca::firstOrCreate(
            ['RAZON_SOCIAL' => $marca]
        );
        return $marca;
    }

    public function obtenerOCrearModelo($modelo, $marca){
        if(strlen($modelo) == 0)
            $modelo = "SinModelo";
            $modelo = Modelo::firstOrCreate(
            ['NOMBRE_FANTASIA' => $modelo],
            ['ID_MARCA' => $marca->ID_MARCA]
        );
        return $modelo;
    }

    public function obtenerOSetearNuloPersona($nombre, $apellido){
        $cliente = Cliente::where([
            ["APELLIDO", $apellido],
            ["NOMBRE", $nombre]])->get();

        if((count($cliente) == 0) or (count($cliente) > 1)){
            Log::Warning("Error in ReflectChangesVehiculos -> newRecords -> El select de cliente a la db, con Nombre y Apellido exacto devolvió 0 o más de uno. Se dejó al vehiculo sin dueño", [$apellido, $nombre]);
            $cliente = [Cliente::firstOrCreate(
                ["NOMBRE" => "ClienteNulo"],
                ["APELLIDO" => "ClienteNulo"]
            )];                  
        }
        return $cliente[0];
    }

    private function crearVehiculo($record, $modelo, $persona){
        Vehiculo::create([
            "PATENTE" => $record->get("PATENTE"),
            "ID_MODELO" => $modelo->ID_MODELO,
            "VIN" => $record->get("VIN"),
            "ANIO" => $record->get("ANIO"),
            "NUMERO_MOTOR" => $record->get("NUMERO_MOTOR"),
            "ID_PERSONA" => $persona->ID_PERSONA,
        ]);
    }

    private function actualizarVehiculo($vehiculo, $record, $persona){
        $vehiculo->VIN = $record->get("VIN");
        $vehiculo->ANIO = $record->get("ANIO");
        $vehiculo->NUMERO_MOTOR = $record->get("NUMERO_MOTOR");
        $vehiculo->ID_PERSONA = $persona->ID_PERSONA;
        $vehiculo->save();
    }

    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);

                $marca = $this->obtenerOCrearMarca($record->get("MARCA"));

                $modelo = $this->obtenerOCrearModelo($record->get("MODELO"), $marca);

                $persona = $this->obtenerOSetearNuloPersona($record->get("NOMBRE"), $record->get("APELLIDO"));

                $vehiculo = Vehiculo::where([
                    ["PATENTE", $record->get("PATENTE")],
                    ["ID_MODELO", $modelo->ID_MODELO]
                ])->get();
                if(count($vehiculo) == 0)
                    $vehiculo = $this->crearVehiculo($record, $modelo, $persona);
                else
                    if(count($vehiculo) == 1)
                        $vehiculo = $this->actualizarVehiculo($vehiculo[0], $record, $persona);
                    else
                        Log::Error("Error in ReflectChangesVehiculos -> newRecords -> El select de vehiculo a la db, dio más de 1", [$record]);
                        
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesVehiculos -> newRecords ->", [$e, $record]);     
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
                Log::Error("Error in ReflectChangesVehiculo -> deletedRecords ->", [$e, $record]);
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
                Log::Error("Error in ReflectChangesVehiculo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}