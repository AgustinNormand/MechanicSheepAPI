<?php
    require __DIR__ . '/../vendor/autoload.php';
    
    use Dotenv\Dotenv;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;
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
        "password" => $config->get("DB_PASSWORD"),
        'sslmode' => env('DB_TYPE') == 'REMOTA' ? 'require' : 'disable',
        'options' => env('DB_TYPE') == 'REMOTA' ? [
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            PDO::MYSQL_ATTR_SSL_KEY => env('DB_CERTIFICATES_PATH', '').'client-key.pem',
            PDO::MYSQL_ATTR_SSL_CERT => env('DB_CERTIFICATES_PATH', '').'client-cert.pem',
            PDO::MYSQL_ATTR_SSL_CA => env('DB_CERTIFICATES_PATH', '').'ca.pem',
        ] : [],
     ]);
     $capsule->setAsGlobal();
     $capsule->bootEloquent();

     $config->loadConfigurations();

     $log = new Logger("MechanicSheepAPI");
     $handler = new StreamHandler($config->get("LOG_PATH"));
     $handler->setLevel($config->get("LOG_LEVEL"));
     $log->pushHandler($handler);
     
