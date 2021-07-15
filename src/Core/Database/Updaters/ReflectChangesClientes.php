<?php

namespace API\Core\Database\Updaters;

use API\Core\Database\Models\DocumentType;
use \Exception;

use API\Core\Database\Models\Person;
use API\Core\Enum\DatabaseColumns\DatabaseColumnsClientes;
use API\Core\Log;

class ReflectChangesClientes
{

    private $clientWithSameDNI;

    private $clientWithSameNameAndSurname;

    public function __construct()
    {
        $this->columns = DatabaseColumnsClientes::$columns;
    }

    public function areClientsWithSameDNI($record){
        $areClientsWithSameDNI = False;
        if(strlen($record->get("DOCUMENT_NUMBER")) > 4){ #Mejorar ese 4 hardcodeado
            $clientes = Person::where("DOCUMENT_NUMBER", $record->get("DOCUMENT_NUMBER"))->get();
            if(count($clientes) == 1){
                $areClientsWithSameDNI = True;
                $this->clientWithSameNameAndSurname = $clientes[0];
            } else
                if(count($clientes) >1)
                    Log::info("No debería haber mas de un cliente con el mismo dni, ya debería haber sido overriden", [$record, $clientes]);
        }
        return $areClientsWithSameDNI;            
    }

    public function areClientsWithSameNameAndSurname($record){
        $areClientsWithSameNameAndSurname = False;
        $clientes = Person::where([["NAME", "=", $record->get("NAME")], ["SURNAME", "=", $record->get("SURNAME")]])->get();
        if(count($clientes) == 1){
            $areClientsWithSameNameAndSurname = True;
            $this->clientWithSameNameAndSurname = $clientes[0];
        } else
            if(count($clientes) >1)
                Log::info("No debería haber mas de un cliente con el mismo nombre y apellido, ya debería haber sido overriden", [$record, $clientes]);
        return $areClientsWithSameNameAndSurname;
    }

    public function isClientToOverride($record){
        return $this->areClientsWithSameNameAndSurname($record) or $this->areClientsWithSameDNI($record);
    }

    public function getClientToOverride(){
        if (is_null($this->clientWithSameDNI))
            return $this->clientWithSameNameAndSurname;
        else
            return $this->clientWithSameDNI;
    }

    public function newRecords($records)
    {
        foreach($records as $record)
        {
            try{
                #Log::Debug("Adding new record to database:", [$record]);

                if(!$this->isClientToOverride($record)){
                    //Si en la base no hay clientes con ese DNI
                    $data = [];
                    foreach($this->columns as $column){
                        $key = array_search($column, $this->columns);
                        $data[$key] = $record->get($key);
                    }
                    $data["ID_DOCUMENT_TYPE"] = DocumentType::firstOrCreate(["DOCUMENT_TYPE" => "DNI"])->ID_DOCUMENT_TYPE;
                    Person::create($data);
                } else{
                    //Si en la base hay un cliente con ese DNI
                    $cliente = $this->getClientToOverride();
                    Log::Info("Overriding existing client on database with record in dbase", [$cliente, $record]);
                    foreach($this->columns as $column)
                    {
                        //Piso los datos que habia en la base, con los datos que habia en DBASE.
                        $key = array_search($column, $this->columns);
                        if(is_null($cliente->$key))
                            $cliente->$key = $record->get($key);
                        else
                            if(is_string($cliente->$key) && strlen($cliente->$key) == 0)
                                $cliente->$key = $record->get($key);
                        $cliente->save();
                    }   
                }   

            }catch(Exception $e){
                Log::Error("ReflectChangesClientes -> newRecords ->", [$e, $record]);     
                die;
            }
        }
    }

/*
    public function deletedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Deleting record to database:", [$record]);
                $cliente = Person::where("DOCUMENT_NUMBER", $record->get('NRO_DOC'))->first();
                //Si hay mas de uno debería tomar una accion diferente
                $cliente->delete();
            } catch(Exception $e){
                Log::Error("ReflectChangesClientes -> deletedRecords ->", [$e, $record]);
            }
        }
    }

    public function modifiedRecords($records)
    {
        foreach($records as $record)
        {
            try{
                Log::Debug("Modifing record in database:", [$record["from"], $record["to"]]);
                #if($record["from"]->getIndex() != $record["to"]->getIndex())
                #    Log::warning("ReflectChangesClientes -> modifiedRecords -> Se está intentando cambiar la clave primaria de un registro", [$record["from"], $record["to"]]);
                $cliente = Person::where("DOCUMENT_NUMBER", $record["from"]->get('NRO_DOC'))->first();
                ##$cliente->ID_CLIENTE = $record["to"]->getIndex();
                if(!is_null($cliente)){
                    foreach($this->columns as $column)
                    {
                        $key = array_search($column, $this->columns);
                        $cliente->$key = $record["to"]->get($key);
                    }
                    $cliente->save();
                } else
                    Log::Error("ReflectChangesClientes -> modifiedRecords -> Se modificó en el desktop un cliente que no se pudo encontrar en la db.", [$record]); 
            } catch(Exception $e){
                Log::Error("ReflectChangesClientes -> modifiedRecords ->", [$e, $record]);
            }
        }
    }*/
}