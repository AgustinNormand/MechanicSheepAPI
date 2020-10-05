<?php

    require "./vendor/autoload.php";
    require_once('Gestor.php');

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    class GestorDistribuido{
        private $gestorClientes;
        private $gestorTrabajos;
        private $gestorVehiculos;
        private $gestorDetalles;

        private $log;

        function __construct(){
            $this->gestorClientes = new Gestor('CLIENTES');
            $this->gestorTrabajos = new Gestor('TRABAJOS');
            $this->gestorVehiculos = new Gestor('VEHICULOS');
            $this->gestorDetalles = new Gestor('DETALLES');

            $this->log = new Logger('GestorDistribuido');
            $this->log->pushHandler(new StreamHandler('GestorDistribuido.log', Logger::WARNING));
        }

        function getClientesPorDni($dni){
            $clientes = $this->gestorClientes->search('dni', $dni);
    
            if (sizeof($clientes) > 1){
                $this->log->warning('GestorDistribuido->getClientesPorDni->Más de 1 cliente con el mismo DNI: '.$dni);
            }
    
            return $clientes;
        }
    
        function getClientesPorApellido($apellido){
            $clientes = $this->gestorClientes->search('apellido', strtoupper($apellido));
            return $clientes;
        }
    
        function getTrabajosPorApellido($apellido){
            $trabajos = $this->gestorTrabajos->search('apellido', strtoupper($apellido));
            return $trabajos;
        }
    
        function getTrabajosPorPatente($patente){
            $trabajos = $this->gestorTrabajos->search('patente', strtoupper($patente));
            return $trabajos;
        }
    
        function getTrabajosPorNumeroTrabajo($numeroTrabajo){
            $trabajos = $this->gestorTrabajos->search('numero', $numeroTrabajo);
            return $trabajos;
        }
    
        function getDetallesPorNumeroTrabajo($numeroTrabajo){
            $detalles = $this->gestorDetalles->search('numero', $numeroTrabajo);
            return $detalles;
        }

        function exportAllCsv(){
            $this->gestorClientes->exportCsv();
            $this->gestorTrabajos->exportCsv();
            $this->gestorDetalles->exportCsv();
            $this->gestorVehiculos->exportCsv();
        }

    }

?>