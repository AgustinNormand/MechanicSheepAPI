<?php

    namespace API\Core;

    use API\Core\Database\Models\Configuration;

    class Config
    {

        private static $instance;
        
        public static function getInstance()
        {   
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
        }

        private array $configs;

        public function __construct()
        {
            $this->configs["DB_CONNECTION"] = getenv("DB_CONNECTION", "mysql");
            $this->configs["DB_HOST"] = getenv("DB_HOST", "127.0.0.1");
            $this->configs["DB_PORT"] = getenv("DB_PORT", "3306");
            $this->configs["DB_DATABASE"] = getenv("DB_DATABASE", "root");
            $this->configs["DB_USERNAME"] = getenv("DB_USERNAME", "root");
            $this->configs["DB_PASSWORD"] = getenv("DB_PASSWORD", "");
        }

        public function loadConfigurations(){
            $this->configs["DBF_FILES_PATH"] = $this->getFromDatabase("DBF_FILES_PATH", ".");

            $this->configs["DBF_CLIENTES_NAME"] = $this->getFromDatabase("DBF_CLIENTES_NAME", "climae.dbf");
            $this->configs["DBF_DETALLES_NAME"] = $this->getFromDatabase("DBF_DETALLES_NAME", "sermae2.dbf");
            $this->configs["DBF_TRABAJOS_NAME"] = $this->getFromDatabase("DBF_TRABAJOS_NAME", "sermae.dbf");
            $this->configs["DBF_VEHICULOS_NAME"] = $this->getFromDatabase("DBF_VEHICULOS_NAME", "vehmae.dbf");
            
            $this->configs["RANGO_IZQ_CLIENTES"] = (int) $this->getFromDatabase("RANGO_IZQ_CLIENTES", 0);
            $this->configs["RANGO_IZQ_DETALLES"] = (int) $this->getFromDatabase("RANGO_IZQ_DETALLES", 0);
            $this->configs["RANGO_IZQ_TRABAJOS"] = (int) $this->getFromDatabase("RANGO_IZQ_TRABAJOS", 0);
            $this->configs["RANGO_IZQ_VEHICULOS"] = (int) $this->getFromDatabase("RANGO_IZQ_VEHICULOS", 0);

            $this->configs["LOG_LEVEL"] = $this->getFromDatabase("LOG_LEVEL", "DEBUG");
            $this->configs["LOG_PATH"] = $this->getFromDatabase("LOG_PATH", "/logs/");

            $this->configs["VERIFY_MODIFICATIONS_TIMER"] = $this->getFromDatabase("VERIFY_MODIFICATIONS_TIMER", 5);

            $this->configs["ONLY_HISTORICAL_RECORDS"] = $this->getFromDatabase("ONLY_HISTORICAL_RECORDS", "true");

            $this->configs["BACKUP_EXTENSION"] = $this->getFromDatabase("BACKUP_EXTENSION", ".bk");   
            
        }

        public function getFromDatabase($name, $default){
            //$configuration = Configuration::where("NAME", $name)->first()->VALUE;
            //return $configuration ?? $default;
            return getenv($name);
        }

        public function get($name)
        {
            return $this->configs[$name] ?? null;
        }

        public function set($name, $value) //This is for test only
        {
            $this->configs[$name] = $value;
        }
    }