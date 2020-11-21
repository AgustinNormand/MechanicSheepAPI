<?php

namespace API\Core\Validators;

use API\Core\Log;

class ValidatorClientes
{
    public static function isValid($record)
    {
        $result = ((strlen($record->get("NOMBRE")) != 0) or (strlen($record->get("APELLIDO")) != 0));

        //if(!$result)
            //Log::info("Record not valid in ValidatorClientes", [$record]);
            
        return $result;
    }
}