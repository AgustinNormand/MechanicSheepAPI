<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Detalle;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsDetalles;
use API\Core\Log;

class ReflectChangesDetalles
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
                $detalle = Detalle::find($record->getIndex());
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
                if($record["from"]->getIndex() != $record["to"]->getIndex())
                    Log::warning("ReflectChangesDetalles -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $detalle = Detalle::find($record["from"]->getIndex());
                $detalle->ID_DETALLE = $record["to"]->getIndex();
                foreach($this->columns as $column)
                {
                    $key = array_search($column, $this->columns);
                    $detalle->$key = $record["to"]->get($key);
                }
                $detalle->save();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesDetalle -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}