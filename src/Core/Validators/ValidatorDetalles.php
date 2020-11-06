<?php

namespace API\Core\Validators;

class ValidatorDetalles
{
    public static function isValid($record)
    {
        $result = false;
        $columnName = "movcom";
        $result = ($record->$columnName != "");
        return $result;
    }
}