<?php

require "./vendor/autoload.php";
require_once('AutenticacionClientes.php');
require_once('GestorDistribuido.php');

class Middleware{
    
    function __construct(){
    }

    function autenticarCliente($apellido, $nombre, $dni){
        autenticarCliente($apellido, $nombre, $dni);
    }

    function getTrabajosAuto($patente){

    }
}

#$middleware = new Middleware();
$gestorDistribuido = new GestorDistribuido();
$gestorDistribuido->exportAllCsv();
#print_r($middleware->getClientesPorDni('16618699'));
#print_r($middleware->getClientesPorApellido('CAERO'));
#$middleware->autenticarCliente('caero', 'jose luis', '16618689');
#print_r($middleware->getTrabajosPorPatente('AD283AR'));
#print_r($middleware->getTrabajosPorApellido('caero'));
#print_r($middleware->getDetallesPorNumeroTrabajo('00014295'));
?>