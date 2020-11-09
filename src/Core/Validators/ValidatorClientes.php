<?php

namespace API\Core\Validators;

class ValidatorClientes
{
    public static function isValid($record)
    {
        $result = ((strlen($record->get("NOMBRE")) != 0) or (strlen($record->get("APELLIDO")) != 0));
        return $result;
    }
}