<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Cliente extends Eloquent
{
    protected $table = "personas";
    protected $primaryKey = 'ID_PERSONA';
    protected $guarded = [];

    public static function obtenerExactoOSetearNuloPersona($nombre, $apellido){
        $cliente = Cliente::where([
            ["APELLIDO", $apellido],
            ["NOMBRE", $nombre]])->get();

        if((count($cliente) == 0) or (count($cliente) > 1)){
            //Log::Warning("Error in ReflectChangesVehiculos -> newRecords -> El select de cliente a la db, con Nombre y Apellido exacto devolvió 0 o más de uno. Se dejó al vehiculo sin dueño", [$apellido, $nombre]);
            $cliente = [Cliente::firstOrCreate(
                ["NOMBRE" => "ClienteNulo"],
                ["APELLIDO" => "ClienteNulo"]
            )];                  
        }
        return $cliente[0];
    }
}
