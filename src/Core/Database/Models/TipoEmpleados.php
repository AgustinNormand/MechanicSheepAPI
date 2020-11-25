<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class TipoEmpleados extends Eloquent
{
    protected $table = "tipo_empleados";
    protected $primaryKey = 'ID_TIPOEMPLEADO';
    protected $guarded = [];

}
