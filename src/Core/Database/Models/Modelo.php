<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Modelo extends Eloquent
{
    protected $table = "modelos";
    protected $primaryKey = 'ID_MODELO';
    protected $guarded = [];
}
