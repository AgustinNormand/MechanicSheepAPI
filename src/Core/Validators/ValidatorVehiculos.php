<?php

namespace API\Core\Validators;

//use API\Core\Log;

class ValidatorVehiculos
{
    public static function isValid($record)
    {
        //if(!$result)
          //  Log::info("Record not valid in ValidatorClientes", [$record]);
        $result = (strlen($record->get("PATENTE")) != 0);

        return $result;
    }
}