<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Enum\DatabaseColumns\DatabaseColumnsTrabajos;
use API\Core\Database\Models\Trabajo;
use API\Core\Log;

class ReflectChangesTrabajos extends ReflectChanges
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
                die;
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            #Trabajo::destroy($record->sernro);
            $trabajo = Trabajo::find($record->sernro);
            #Log::debug("Deleting", [$trabajo]);
            $trabajo->delete();
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            $trabajo = Trabajo::find($record["from"]->sernro);
            $trabajo->MARCA = $record["to"]->sermar;
            $trabajo->MODELO = $record["to"]->sermod;
            $trabajo->APELLIDO = $record["to"]->serape;
            $trabajo->NOMBRE = $record["to"]->sernom;
            $trabajo->FECHA = $record["to"]->serfec;
            $trabajo->save();
        }
    }
}