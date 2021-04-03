<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Brand extends Eloquent
{
    protected $table = "BRANDS";
    protected $primaryKey = 'ID_BRAND';
    protected $guarded = [];

    public static function obtenerOCrearMarca($marca){
        if(strlen($marca) == 0)
            $marca = "SinMarca";

        $marca = self::firstOrCreate(
                ['NAME' => $marca]
            );
        return $marca;
    }
}
