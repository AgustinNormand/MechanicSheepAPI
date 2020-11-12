<?php
    require __DIR__ . '/../vendor/autoload.php';
    
    use Dotenv\Dotenv;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Illuminate\Database\Capsule\Manager as Capsule;

    use API\Core\Config;

    $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
    $dotenv->load();

    $config = Config::getInstance();

    $capsule = new Capsule;
    $capsule->addConnection([
        "driver" => $config->get("DB_CONNECTION"),
        "host" => $config->get("DB_HOST"),
        "port" => $config->get("DB_PORT"),
        "database" => $config->get("DB_DATABASE"),
        "username" => $config->get("DB_USERNAME"),
        "password" => $config->get("DB_PASSWORD")
     ]);
     $capsule->setAsGlobal();
     $capsule->bootEloquent();

     $config->loadConfigurations();

     $log = new Logger("MechanicSheepAPI");
     $handler = new StreamHandler($config->get("LOG_PATH"));
     $handler->setLevel($config->get("LOG_LEVEL"));
     $log->pushHandler($handler);
     
