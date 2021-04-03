<?php

namespace API\Core\Database\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Person extends Eloquent
{
    protected $table = "PERSONS";
    protected $primaryKey = 'ID_PERSON';
    protected $guarded = [];

    public static function obtenerExactoOSetearNuloPersona($nombre, $apellido){
        $cliente = Person::where([
            ["SURNAME", $apellido],
            ["NAME", $nombre]])->get();

        if((count($cliente) == 0) or (count($cliente) > 1)){
            //Log::Warning("ReflectChangesVehiculos -> newRecords -> El select de cliente a la db, con Nombre y Apellido exacto devolvió 0 o más de uno. Se dejó al vehiculo sin dueño", [$apellido, $nombre]);
            $cliente = [Person::firstOrCreate(
                ["NAME" => "ClienteNulo"],
                ["SURNAME" => "ClienteNulo"]
            )];                  
        }
        return $cliente[0];
    }
}
