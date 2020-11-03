<?php
    require '..' . DIRECTORY_SEPARATOR .'bootstrap.php';

    //Borrar esto, es para developent nada mas
    if ($handle = opendir("Z:\API\UntouchedDatabases")) 
        while (false !== ($entry = readdir($handle))) 
            if ($entry != "." && $entry != "..") 
                copy("Z:\API\UntouchedDatabases\\" . $entry, 'C:/Users/Windows/Desktop/Sistema Mechanic Sheep/Core (CREO)/visual/OVEJA/' . $entry);
    closedir($handle);  
    //Borrar esto, es para developent nada mas

    
    use API\Core\Watchdog;

    $wd = new Watchdog;

    $wd->loopForever();