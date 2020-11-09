<?php

namespace API\Core\Validators;

use API\Core\Config;

class ValidatorDetalles
{
    public static function isValid($record)
    {
        $onlyHistoricals = (Config::getInstance()->get("ONLY_HISTORICAL_RECORDS") == "true");

        if(!$onlyHistoricals)
            $result = true;
        else
        {
            $result = false;
            $columnName = "movcom";
            $result = ($record->$columnName != "");
        }
        return $result;
    }
}