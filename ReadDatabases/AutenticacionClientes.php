<?php

    require_once("GestorDistribuido.php");

    $GestorDistribuido = new GestorDistribuido();

    function autenticarCliente($apellido, $nombre, $dni){
        
        $autenticated = autenticarPorDni($apellido, $nombre, $dni);
        if ($autenticated){
            echo 'Client autenticated';
        } else{
            echo 'DNI autentication failed';
            echo '<br>';
            echo '<br>';
            $autenticated = autenticarPorNombreApellido($apellido, $nombre, $dni);
        }
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
        foreach($clientes as $cliente){
            if ($cliente['nombre'] == strtoupper($nombre)){
                $autenticated = true;
                echo 'Dni ingresado: '.$dni;
                echo '<br>';
                echo 'Dni sistema: '.$cliente['dni'];
            }

            return $autenticated;
        }
    }
?>