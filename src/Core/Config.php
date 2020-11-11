<?php

    namespace API\Core;

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
            $this->configs["DBF_FILES_PATH"] = getenv("DBF_FILES_PATH", "");

            $this->configs["DBF_CLIENTES_NAME"] = getenv("DBF_CLIENTES_NAME", "climae.dbf");
            $this->configs["DBF_DETALLES_NAME"] = getenv("DBF_DETALLES_NAME", "sermae2.dbf");
            $this->configs["DBF_TRABAJOS_NAME"] = getenv("DBF_TRABAJOS_NAME", "sermae.dbf");
            $this->configs["DBF_VEHICULOS_NAME"] = getenv("DBF_VEHICULOS_NAME", "vehmae.dbf");
            
            $this->configs["RANGO_IZQ_CLIENTES"] = (int) getenv("RANGO_IZQ_CLIENTES", null);
            $this->configs["RANGO_IZQ_DETALLES"] = (int) getenv("RANGO_IZQ_DETALLES", null);
            $this->configs["RANGO_IZQ_TRABAJOS"] = (int) getenv("RANGO_IZQ_TRABAJOS", null);
            $this->configs["RANGO_IZQ_VEHICULOS"] = (int) getenv("RANGO_IZQ_VEHICULOS", null);

            $this->configs["LOG_LEVEL"] = getenv("LOG_LEVEL", "DEBUG");
            $this->configs["LOG_PATH"] = getenv("LOG_PATH", "/logs/");

            $this->configs["VERIFY_MODIFICATIONS_TIMER"] = getenv("VERIFY_MODIFICATIONS_TIMER", 5);

            $this->configs["ONLY_HISTORICAL_RECORDS"] = getenv("ONLY_HISTORICAL_RECORDS", "true");

            $this->configs["DB_CONNECTION"] = getenv("DB_CONNECTION", "mysql");
            $this->configs["DB_HOST"] = getenv("DB_HOST", "127.0.0.1");
            $this->configs["DB_PORT"] = getenv("DB_PORT", "3306");
            $this->configs["DB_DATABASE"] = getenv("DB_DATABASE", "root");
            $this->configs["DB_USERNAME"] = getenv("DB_USERNAME", "root");
            $this->configs["DB_PASSWORD"] = getenv("DB_PASSWORD", "");

            $this->configs["BACKUP_EXTENSION"] = getenv("BACKUP_EXTENSION", ".bk");
        }

        public function get($name)
        {
            return $this->configs[$name] ?? null;
        }
    }