<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Vehiculo extends Eloquent
{
    protected $table = "vehiculos";
    protected $primaryKey = 'ID_VEHICULO';
    protected $guarded = [];

}
