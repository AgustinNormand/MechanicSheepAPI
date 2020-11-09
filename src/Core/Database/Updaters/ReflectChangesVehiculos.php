<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Vehiculo;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsVehiculos;
use API\Core\Log;

class ReflectChangesVehiculos extends ReflectChanges
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
                foreach($this->columns as $column){
                    $key = array_search($column, $this->columns);
                    $data[$key] = $record->get($key);
                }
                $data['ID_VEHICULO'] = $record->getIndex();
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
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
        }
    }
}