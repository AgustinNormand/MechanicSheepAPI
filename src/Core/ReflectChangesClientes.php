<?php

namespace API\Core;

use \Exception;

use API\Core\Cliente;

class ReflectChangesClientes extends ReflectChanges
{
    public function newRecords($records)
    {
        
        foreach($records as $record)
        {
            #var_dump($record);
            try{
                $cliente = Cliente::create([
                    'id' => $record->clidoc,
                    'APELLIDO' => $record->cliape,
                    'NOMBRE' => $record->clinom,
                    'TELEFONO' => $record->clitel,
                    'DIRECCION' => $record->clidir,
                    'LOCALIDAD' => $record->cliloc,
                    'CODIGO_POSTAL' => $record->clicpo,
                    'BARRIO' => $record->clibar,
                    'CUIT' => $record->clicui,
                    'CONDICION_IVA' => $record->cliiva,
                    'EMAIL' => $record->climai,
                ]);
            }catch(Exception $e){
                echo $e;
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            $cliente = Cliente::find($record->clidoc);
            $cliente->delete();
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            $cliente = Cliente::find($record["from"]->clidoc);
            $cliente->id = $record["to"]->clidoc;
            $cliente->APELLIDO = $record["to"]->cliape;
            $cliente->NOMBRE = $record["to"]->clinom;
            $cliente->TELEFONO = $record["to"]->clitel;
            $cliente->DIRECCION = $record["to"]->clidir;
            $cliente->LOCALIDAD = $record["to"]->cliloc;
            $cliente->CODIGO_POSTAL = $record["to"]->clicpo;
            $cliente->BARRIO = $record["to"]->clibar;
            $cliente->CUIT = $record["to"]->clicui;
            $cliente->CONDICION_IVA = $record["to"]->cliiva;
            $cliente->EMAIL = $record["to"]->climai;
            $cliente->save();
        }
    }
}