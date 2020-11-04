<?php

namespace API\Core;

use \Exception;

use API\Core\Trabajo;

class ReflectChangesTrabajos extends ReflectChanges
{
    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                $trabajo = Trabajo::create([
                    'id' => $record->sernro,
                    'MARCA' => $record->sermar,
                    'MODELO' => $record->sermod,
                    'APELLIDO' => $record->serape,
                    'NOMBRE' => $record->sernom,
                    'FECHA' => $record->serfec,
                ]);
            }catch(Exception $e){}
        }
    }

    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            #Trabajo::destroy($record->sernro);
            $trabajo = Trabajo::find($record->sernro);
            #Log::debug("Deleting", [$trabajo]);
            $trabajo->delete();
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            $trabajo = Trabajo::find($record["from"]->sernro);
            $trabajo->MARCA = $record["to"]->sermar;
            $trabajo->MODELO = $record["to"]->sermod;
            $trabajo->APELLIDO = $record["to"]->serape;
            $trabajo->NOMBRE = $record["to"]->sernom;
            $trabajo->FECHA = $record["to"]->serfec;
            $trabajo->save();
        }
    }
}