<?php

namespace API\Core\Database\Updaters;

use \Exception;
use API\Core\Database\Models\Detalle;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsDetalles;
use API\Core\Log;

class ReflectChangesDetalles extends ReflectChanges
{
    public function __construct()
    {
        $this->columns = DatabaseColumnsDetalles::$columns;
    }

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
                $data['ID_DETALLE'] = $record->getIndex();
                Detalle::create($data);
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalles -> newRecords ->", [$e, $record]);     
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
                $detalle = detalle::find($record->get("DNI"));
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
                ##
                $detalle = Detalle::find($record["from"]->get("DNI"));
                $detalle->id = $record["to"]->get("DNI");
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $detalle->$column = $record["to"]->get($column);
                }
                $detalle->save();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalles -> modifiedRecords ->", [$e, $record]);
            } 
        }
    }
}