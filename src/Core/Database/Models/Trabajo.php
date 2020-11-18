<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Trabajo extends Eloquent
{
    protected $table = "trabajos";
    protected $primaryKey = 'ID_TRABAJO';
    protected $guarded = [];

}
