<?php

    require_once("GestorDistribuido.php");

    $GestorDistribuido = new GestorDistribuido();

    #print_r($GestorDistribuido->getClientesPorApellido('CAERO'));
    #print_r($GestorDistribuido->getClientesPorDni('16618699'));

    function autenticarCliente($apellido, $nombre, $dni){
        
        #$autenticated = autenticarPorDni($apellido, $nombre, $dni);
        #if ($autenticated){
        #    echo 'Client autenticated';
        #} else{
        #    autenticarPorNombreApellido($apellido, $nombre, $dni);
        #}
    }

    function autenticarPorDni($apellido, $nombre, $dni){
        global $GestorDistribuido;
        $autenticated = false;

        echo 'Trying DNI autentication';
        echo '<br>';
        $clientes = $GestorDistribuido->getClientesPorDni($dni);
        if (!empty($clientes)){
            foreach($clientes as $cliente){
                if ($cliente['apellido'] == strtoupper($apellido) and $cliente['nombre'] == strtoupper($nombre)){
                    $autenticated = true;
                }
                return $autenticated;
            }
        }
    }

    function autenticarPorNombreApellido($apellido, $nombre, $dni){
        global $GestorDistribuido;
        $autenticated = false;

        echo 'Trying Nombre-Apellido autentication';
        echo '<br>';
        $clientes = $GestorDistribuido->getClientesPorApellido($apellido);
        print_r($clientes);
        foreach($clientes as $cliente){
            if ($cliente['nombre'] == strtoupper($nombre)){
                $autenticated = true;
                echo 'Dni ingresado: '.$dni;
                echo 'Dni sistema: '.$cliente['dni'];
            }

            return $autenticated;
        }
    }
?>