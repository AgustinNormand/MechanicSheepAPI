<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cliente extends Eloquent
{
    protected $table = "personas";
    protected $primaryKey = 'ID_PERSONA';
    protected $guarded = [];
}
