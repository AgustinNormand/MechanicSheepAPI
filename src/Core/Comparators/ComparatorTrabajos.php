<?php

namespace API\Core\Comparators;

use API\Core\Comparators\ComparatorBase;
use API\Core\Log;
use API\Core\Validators\ValidatorTrabajos;

class ComparatorTrabajos extends ComparatorBase
{
    function addModifiedRecord($from, $to){
        if (!$this->exists($from, $this->modifiedRecordsFound) and ValidatorTrabajos::isValid($from) and ValidatorTrabajos::isValid($to))
        {
            $index = count($this->modifiedRecordsFound);
            $this->modifiedRecordsFound[$index]["from"] = $from;
            $this->modifiedRecordsFound[$index]["to"] = $to;
            Log::info("Modified from: ", [$this->toString($from)]);
            Log::info("Modified to: ", [$this->toString($to)]);
        }
        else
            if(!ValidatorTrabajos::isValid($from) and ValidatorTrabajos::isValid($to))
                $this->addNewRecord($to);
        
    }

    function addNewRecord($record){
        if (!$this->exists($record, $this->newRecordsFound) and ValidatorTrabajos::isValid($record)){
            $this->newRecordsFound[] = $record;
            Log::info("New: ", [$this->toString($record)]);
        }
    }

    function addDeletedRecord($record){
        if (!$this->exists($record, $this->deletedRecordsFound) and ValidatorTrabajos::isValid($record)){
            $this->deletedRecordsFound[] = $record;
            Log::info("Deleted: ", [$this->toString($record)]);
        }
    }
}