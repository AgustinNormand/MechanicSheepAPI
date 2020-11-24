<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use API\Core\Database\Models\Empleado;

class Taller extends Eloquent
{
    protected $table = "tallers";
    protected $primaryKey = 'ID_TALLER';
    protected $guarded = [];
}
