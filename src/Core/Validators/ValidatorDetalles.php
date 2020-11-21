<?php

namespace API\Core\Validators;

use API\Core\Config;
use API\Core\Log;

class ValidatorDetalles
{
    public static function isValid($record)
    {
        $onlyHistoricals = (Config::getInstance()->get("ONLY_HISTORICAL_RECORDS") == "true");
/*
        if($record->getIndex() == 9){
            echo $onlyHistoricals;
            var_dump($record);
            die;
        }
            */
        if(!$onlyHistoricals)
            $result = true;
        else
        {
            //$result = false;
            #$columnName = "movcom";
            //var_dump($record);
            $result = (strlen($record->get("COMPROBANTE")) != 0);

            //if($result)
            //    echo "True";
            //else
            //    echo "False";
            //die;
        }

        //if(!$result)
            //Log::info("Record not valid in ValidatorClientes", [$record]);

        return $result;
    }
}