<?php
    require __DIR__ . '/../vendor/autoload.php';
    
    use Dotenv\Dotenv;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    use API\Core\Config;

    $dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
    $dotenv->load();

    $config = Config::getInstance();

    $log = new Logger("MechanicSheepAPI");
    $handler = new StreamHandler($config->get("LOG_PATH"));
    $handler->setLevel($config->get("LOG_LEVEL"));
    $log->pushHandler($handler);

