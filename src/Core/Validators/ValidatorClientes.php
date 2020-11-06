<?php

namespace API\Core\Validators;

class ValidatorClientes
{
    public static function isValid($record)
    {
        $result = false;
        $apellido = "cliape";
        $nombre = "clinom";
        $result = ((strlen($record->$nombre) != 0) or (strlen($record->$apellido) != 0));
        return $result;
    }
}