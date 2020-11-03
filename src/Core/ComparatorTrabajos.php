<?php

namespace API\Core;

use API\Core\Comparator;

class ComparatorTrabajos extends Comparator
{
    function isHistoric($record)
    {
        $result = false;
        $columnName = "serest";
        $result = ($record->$columnName == 'T');
        return $result;
    }

    function addModifiedRecord($from, $to){
        if (!$this->exists($from, $this->modifiedRecordsFound) and $this->isHistoric($from) and $this->isHistoric($to))
        {
            $index = count($this->modifiedRecordsFound);
            $this->modifiedRecordsFound[$index]["from"] = $from;
            $this->modifiedRecordsFound[$index]["to"] = $to;
            Log::info("Modified from: ", [$this->toString($from)]);
            Log::info("Modified to: ", [$this->toString($to)]);
        }
        else
            if(!$this->isHistoric($from) and $this->isHistoric($to))
                $this->addNewRecord($to);
        
    }

    function addNewRecord($record){
        if (!$this->exists($record, $this->newRecordsFound) and $this->isHistoric($record)){
            $this->newRecordsFound[] = $record;
            Log::info("New: ", [$this->toString($record)]);
        }
    }

    function addDeletedRecord($record){
        if (!$this->exists($record, $this->deletedRecordsFound) and $this->isHistoric($record)){
            $this->deletedRecordsFound[] = $record;
            Log::info("Deleted: ", [$this->toString($record)]);
        }
    }
}