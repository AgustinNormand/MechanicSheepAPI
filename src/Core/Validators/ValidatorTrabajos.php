<?php

namespace API\Core\Validators;

class ValidatorTrabajos
{
    public static function isValid($record)
    {
        $result = false;
        $columnName = "serest";
        $result = ($record->$columnName == 'T');
        return $result;
    }
}