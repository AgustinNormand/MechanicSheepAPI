<?php

namespace API\Core\Database\Records;

use API\Core\Log;

class Record
{
    private $data = [];

    private $isDeleted;

    private $parches = [
        'Ñ' => 165,
        'Á' => 181,
        'É' => 144,
        'Í' => 214,
        'Ó' => 224,
        'Ú' => 233,
        'ñ' => 164,
        'á' => 160,
        'é' => 130,
        'í' => 161,
        'ó' => 162,
        'ú' => 163,
        '°' => 248,
        'º' => 167,
        '´' => 239,
        '·' => 250,
        'È' => 212,
    ];

    public function __construct($index, $data, $isDeleted=false)
    { 
        $this->isDeleted = $isDeleted;
        $this->data = $data;
        $this->index = $index;

        //if($this->index == 5153)
       // {var_dump($this);}

        $keys = array_keys($this->data);
        foreach($keys as $key){
            $value = $this->data[$key];
            if(!is_null($value) and is_string($value)){
                for ( $pos=0; $pos < strlen($value); $pos ++ ) { //Recorro todos los caracteres del dato
                    $byte = substr($value, $pos);
                    $ordValue = ord($byte);
                    if ($ordValue >= 128){ //Si hay algún caracter que no sea válido
                        $this->data[$key] = $this->fixStringErrors($value);
                        break;
                    }
                }
            }
        }
        /*
        foreach($this->data as $value)
            if(!is_null($value) and is_string($value)){
                $key = array_search($value, $this->data);
                for ( $pos=0; $pos < strlen($value); $pos ++ ) { //Recorro todos los caracteres del dato
                    $byte = substr($value, $pos);
                    $ordValue = ord($byte);
                    if ($ordValue >= 128){ //Si hay algún caracter que no sea válido
                        
                        if($index == 1658){
                            var_dump($value);
                            echo $key;
                            die;
                        }
                        $this->data[$key] = $this->fixStringErrors($value);
                        break;
                    }
                }
            } 
            */
    }

    public function __toString()
    {
        /*
        $result = "";
        $result = $this->index . ' ';
        foreach($this->data as $value)
            $result = $result . $value . ' ';
        return $result;
        */

        $result = "{INDEX: " . $this->index . ',';
        $keys = array_keys($this->data);
        foreach($keys as $key)
            $result = $result . " " . $key . ': ' . $this->data[$key] . ",";
        $result = $result . "}";
        return $result;
    }

    public function getIndex()
    {
        //var_dump($this);
        return $this->index;
    }

    public function get($name)
    {
        $result = $this->data[$name] ?? null;
        return $result;
    }

    public function equals($record){
        return $this == $record;
    }

    public function isDeleted()
    {
        return $this->isDeleted;
    }

    private function fixStringErrors($value) #Esto es lo más flojo, sería lo próximo a mejorar
    {
        $value = str_replace(chr(209), chr(165), $value);
        $value = str_replace(chr(243), chr(162), $value);
        $value = str_replace(chr(232), chr(165), $value);
        $value = str_replace(chr(211), chr(224), $value);
        $value = str_replace(chr(237), chr(161), $value);
        $value = str_replace(chr(205), chr(214), $value);
        $value = str_replace(chr(210), chr(224), $value);
        $value = str_replace(chr(193), chr(181), $value);

        if($this->index == 3493)
            $value = str_replace(chr(128), "L", $value);
        if($this->index == 8793)
            $value = str_replace(chr(128), "C", $value);
        if($this->index == 17463)
            $value = str_replace(chr(128), "F", $value);
        if($this->index == 154847)
            $value = str_replace(chr(241), "ñ", $value);
        if($this->index == 217525)
            $value = str_replace(chr(241), "t", $value);


        for ( $pos=0; $pos < strlen($value); $pos ++ ) {
            $byte = substr($value, $pos);
            $ordValue = ord($byte);
            if($ordValue >= 128 and (!in_array($ordValue, $this->parches))){
                Log::alert("fixStringErrors -> Fué necesario suprimir el caracter no válido numero {$pos}, ord: {$ordValue}, record index: {$this->index}.", [$value]);
                $value = str_replace($byte, "", $value);
            }
        }
        foreach($this->parches as $parche)
        {
            $key = array_search($parche, $this->parches);
            $value = str_replace(chr($parche), $key, $value);
        }
        return $value;
    }

}