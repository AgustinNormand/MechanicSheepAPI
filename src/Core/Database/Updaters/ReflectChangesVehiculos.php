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
                $data = [];
                $data["PATENTE"] = $record->get("PATENTE");
                $data["VIN"] = $record->get("VIN");
                $data["ANIO"] = $record->get("ANIO");
                $data["NUMERO_MOTOR"] = $record->get("NUMERO_MOTOR");

                $apellidoCliente = $record->get("APELLIDO");
                $nombreCliente = $record->get("NOMBRE");

                $cliente = Cliente::where([
                    ["APELLIDO", $apellidoCliente],
                    ["NOMBRE", $nombreCliente]])->get();

                if((count($cliente) == 0) or (count($cliente) > 1)){
                    Log::Warning("Error in ReflectChangesVehiculos -> newRecords -> El select de cliente a la db, con Nombre y Apellido exacto devolvió 0 o más de uno. Se dejó al vehiculo sin dueño", [$record]);
                    $cliente = [Cliente::firstOrCreate(
                        ["NOMBRE" => "ClienteNulo"],
                        ["APELLIDO" => "ClienteNulo"]
                    )];                  
                } 
                $data["ID_PERSONA"] = $cliente[0]->ID_PERSONA;

                $nombreMarca = $record->get("MARCA");
                if(strlen($nombreMarca) == 0)
                    $nombreMarca = "SinMarca";

                $marca = Marca::firstOrCreate(
                    ['RAZON_SOCIAL' => $nombreMarca]
                );

                $nombreModelo = $record->get("MODELO");
                if(strlen($nombreModelo) == 0)
                    $nombreModelo = "SinModelo";
                $modelo = Modelo::firstOrCreate(
                    ['NOMBRE_FANTASIA' => $nombreModelo],
                    ['ID_MARCA' => $marca->ID_MARCA]
                );

                $modelo->ID_MARCA = $marca->ID_MARCA;
                $modelo->save();

                $data["ID_MODELO"] = $modelo->ID_MODELO;
                Vehiculo::create($data);
                
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