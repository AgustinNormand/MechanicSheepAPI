<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Detail extends Eloquent
{
    protected $guarded = [];
    protected $table = 'DETAILS';
    protected $primaryKey = "ID_DETAIL";

}
