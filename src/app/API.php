<?php
    require '..' . DIRECTORY_SEPARATOR .'bootstrap.php';

    //Borrar esto, es para developent nada mas
    /*
    $ds = DIRECTORY_SEPARATOR;
    if ($handle = opendir(__DIR__.$ds.'..'.$ds.'..'.$ds."UntouchedDatabases")) 
        while (false !== ($entry = readdir($handle))) 
            if ($entry != "." && $entry != "..") 
                copy(__DIR__.$ds.'..'.$ds.'..'.$ds."UntouchedDatabases". $ds . $entry, 'C:/Users/Windows/Desktop/Sistema Mechanic Sheep/Core (CREO)/visual/OVEJA/' . $entry);
    closedir($handle);  */
    //Borrar esto, es para developent nada mas

    
    use API\Core\Watchdog;

    $wd = new Watchdog;

    $wd->loopForever();