<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Modelo extends Eloquent
{
    protected $table = "modelos";
    protected $primaryKey = 'ID_MODELO';
    protected $guarded = [];

    public static function obtenerOCrearModelo($modelo, $idMarca){
        if(strlen($modelo) == 0)
            $modelo = "SinModelo";

        $modelo = self::firstOrCreate(
            [
                'NOMBRE_FANTASIA' => $modelo,
                'ID_MARCA' => $idMarca
            ]
        );
        return $modelo;
    }
}
