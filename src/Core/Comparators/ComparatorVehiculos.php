<?php

namespace API\Core\Comparators;

use API\Core\Comparators\ComparatorBase;
use API\Core\Log;

class ComparatorVehiculos extends ComparatorBase
{
    function isValid($record)
    {
        $result = false;
        $columnName = "cliape";
        $result = (strlen($record->$columnName) != 0);
        return $result;
    }

    function addModifiedRecord($from, $to){
        if (!$this->exists($from, $this->modifiedRecordsFound) and $this->isValid($from))
        {
            $index = count($this->modifiedRecordsFound);
            $this->modifiedRecordsFound[$index]["from"] = $from;
            $this->modifiedRecordsFound[$index]["to"] = $to;
            Log::info("Modified from: ", [$this->toString($from)]);
            Log::info("Modified to: ", [$this->toString($to)]);
        }
        else
            if(!$this->isValid($from) and $this->isValid($to))
                $this->addNewRecord($to);
        
    }

    function addNewRecord($record){
        if (!$this->exists($record, $this->newRecordsFound) and $this->isValid($record)){
            $this->newRecordsFound[] = $record;
            Log::info("New: ", [$this->toString($record)]);
        }
    }

    function addDeletedRecord($record){
        if (!$this->exists($record, $this->deletedRecordsFound) and $this->isValid($record)){
            $this->deletedRecordsFound[] = $record;
            Log::info("Deleted: ", [$this->toString($record)]);
        }
    }
}