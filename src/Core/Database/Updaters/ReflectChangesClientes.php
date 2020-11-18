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
/*
    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);
                $data = [];
                foreach($this->columns as $column){
                    $key = array_search($column, $this->columns);
                    $data[$key] = $record->get($key);
                }
                $data['ID_CLIENTE'] = $record->getIndex();
                Cliente::create($data);
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> newRecords ->", [$e, $record]);     
                #die;
            }
        }
    }
*/

    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Adding new record to database:", [$record]);
                $data = [];
                foreach($this->columns as $column){
                    $key = array_search($column, $this->columns);
                    $data[$key] = $record->get($key);
                }
                ##$data['ID_CLIENTE'] = $record->getIndex();
                Cliente::create($data);
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> newRecords ->", [$e, $record]);     
                #die;
            }
            #die;
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $cliente = Cliente::find($record->getIndex());
                $cliente->delete();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> deletedRecords ->", [$e, $record]);
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
                    Log::warning("ReflectChangesClientes -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $cliente = Cliente::find($record["from"]->getIndex());
                $cliente->ID_CLIENTE = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $cliente->$key = $record["to"]->get($key);
                }
                $cliente->save();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}