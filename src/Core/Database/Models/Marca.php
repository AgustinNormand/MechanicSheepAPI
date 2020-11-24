<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Marca extends Eloquent
{
    protected $table = "marcas";
    protected $primaryKey = 'ID_MARCA';
    protected $guarded = [];

    public static function obtenerOCrearMarca($marca){
        if(strlen($marca) == 0)
            $marca = "SinMarca";

        $marca = self::firstOrCreate(
                ['RAZON_SOCIAL' => $marca]
            );
        return $marca;
    }
}
