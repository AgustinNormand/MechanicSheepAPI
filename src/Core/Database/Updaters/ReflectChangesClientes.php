<?php

namespace API\Core\Database\Updaters;

use \Exception;

use API\Core\Database\Models\Cliente;
use API\Core\Log;

class ReflectChangesClientes extends ReflectChanges
{
    public function newRecords($records)
    {
        
        foreach($records as $record)
        {
            #var_dump($record);
            #die;
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
                Log::Error("Error in ReflectChangesClientes -> newRecords ->", [$e, $record]);
                #echo $e;
                #var_dump($record);
                #die;
                
            }
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                $cliente = Cliente::find($record->clidoc);
                $cliente->delete();
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            try{
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
            } catch(Exception $e){
                Log::Error("Error in ReflectChangesClientes -> modifiedRecords ->", [$e, $record]);
            }
        }
    }
}