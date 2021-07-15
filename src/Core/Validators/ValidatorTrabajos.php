<?php

namespace API\Core\Validators;

use API\Core\Config;
use API\Core\Log;

class ValidatorTrabajos
{
    public static function isValid($record)
    {
        $onlyHistoricals = (Config::getInstance()->get("ONLY_HISTORICAL_RECORDS") == "true");

        if(!$onlyHistoricals)
            $result = true;
        else
        {
            $result = false;
            $result = ($record->get("ESTADO") == 'T');
            if(!$result)
            Log::debug("Record not valid in ValidatorTrabajos", [$record]);
        }

        

        return $result;
    }
}