<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use API\Core\Database\Models\Empleado;

class Sector extends Eloquent
{
    protected $table = "sectors";
    protected $guarded = [];
}
