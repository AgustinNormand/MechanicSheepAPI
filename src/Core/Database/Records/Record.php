<?php

namespace API\Core\Database\Records;

class Record
{
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get($name)
    {
        return $this->data[$name];
    }
}