<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    protected $table = "MODELS";
    protected $primaryKey = 'ID_MODEL';
    protected $guarded = [];

    public static function obtenerOCrearModelo($modelo, $idMarca){
        if(strlen($modelo) == 0)
            $modelo = "SinModelo";

        $modelo = self::firstOrCreate(
            [
                'NAME' => $modelo,
                'ID_BRAND' => $idMarca
            ]
        );
        return $modelo;
    }
}
