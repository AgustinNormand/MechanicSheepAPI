<?php

namespace API\Core\Validators;

use API\Core\Log;

//use API\Core\Log;

class ValidatorVehiculos
{
    public static function isValid($record)
    {

      $result = (strlen($record->get("NUMBER_PLATE")) != 0);

        if(!$result)
            Log::debug("Record not valid in ValidatorVehiculos", [$record]);

        return $result;
    }
}