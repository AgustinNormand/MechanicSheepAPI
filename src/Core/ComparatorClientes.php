<?php

namespace API\Core;

use API\Core\Comparator;

use function PHPUnit\Framework\isNull;

class ComparatorClientes extends Comparator
{
    function isValid($record)
    {
        $result = false;
        $columnName = "cliape";
        $result = (strlen($record->$columnName) != 0);
        #if($result){
        #    Log::debug("Is valid", [$record->$columnName]);
        #    Log::debug('Lenght', [strlen($record->$columnName)]);
        #}
        #else{
        #    Log::debug("Is not valid", [$record->$columnName]);
        #    Log::debug('Lenght', [strlen($record->$columnName)]);
        #}
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