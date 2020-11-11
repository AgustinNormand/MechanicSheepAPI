<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Trabajo;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsTrabajos;
use API\Core\Log;

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
                $data = [];
                foreach($this->columns as $column){
                    $key = array_search($column, $this->columns);
                    $data[$key] = $record->get($key);
                }
                $data['ID_TRABAJO'] = $record->getIndex();
                Trabajo::create($data);
            }catch(Exception $e){
                Log::Error("Error in ReflectChangesTrabajos -> newRecords ->", [$e, $record]);
                #die;
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
                Log::Error("Error in ReflectChangesTrabajos -> deletedRecords ->", [$e, $record]);
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
                Log::Error("Error in ReflectChangesTrabajo -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}