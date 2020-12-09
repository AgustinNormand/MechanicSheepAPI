<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Cliente;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsClientes;
use API\Core\Log;

class ReflectChangesClientes
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsClientes::$columns;
    }

    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);
                $cliente = Cliente::where("NRO_DOC", $record->get("NRO_DOC"))->first();
                //Si hay mas de uno debería tomar una accion diferente
                if(is_null($cliente)){
                    $data = [];
                    foreach($this->columns as $column){
                        $key = array_search($column, $this->columns);
                        $data[$key] = $record->get($key);
                    }
                    Cliente::create($data);
                } else{
                    foreach($this->columns as $column)
                    {
                        $key = array_search($column, $this->columns);
                        if(is_null($cliente->$key))
                            $cliente->$key = $record->get($key);
                        else
                            if(is_string($cliente->$key) && strlen($cliente->$key) == 0)
                                $cliente->$key = $record->get($key);
                        $cliente->save();
                    }   
                }
            }catch(Exception $e){
                Log::Error("ReflectChangesClientes -> newRecords ->", [$e, $record]);     
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $cliente = Cliente::where("NRO_DOC", $record->get('NRO_DOC'))->first();
                //Si hay mas de uno debería tomar una accion diferente
                $cliente->delete();
            } catch(Exception $e){
                Log::Error("ReflectChangesClientes -> deletedRecords ->", [$e, $record]);
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
                #    Log::warning("ReflectChangesClientes -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $cliente = Cliente::where("NRO_DOC", $record["from"]->get('NRO_DOC'))->first();
                ##$cliente->ID_CLIENTE = $record["to"]->getIndex();
                if(!is_null($cliente)){
                    foreach($this->columns as $column)
                    {
                        $key = array_search($column, $this->columns);
                        $cliente->$key = $record["to"]->get($key);
                    }
                    $cliente->save();
                } else
                    Log::Error("ReflectChangesClientes -> modifiedRecords -> Se modificó en el desktop un cliente que no se pudo encontrar en la db.", [$e, $record]); 
            } catch(Exception $e){
                Log::Error("ReflectChangesClientes -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}