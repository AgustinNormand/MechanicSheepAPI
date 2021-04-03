<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class DocumentType extends Eloquent
{
    protected $table = "DOCUMENT_TYPES";
    protected $primaryKey = 'ID_DOCUMENT_TYPE';
    protected $guarded = [];

}
