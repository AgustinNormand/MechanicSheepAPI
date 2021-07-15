<?php

namespace API\Core;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Log
{
    protected static $instance;

    static public function getLogger()
	{
		if (! self::$instance) {
			self::configureInstance();
		}

		return self::$instance;
    }
    
    protected static function configureInstance()
	{
		$ds = DIRECTORY_SEPARATOR;
        $config = Config::getInstance();
		self::$instance = new Logger("MechanicSheepAPI");
        $handler = new StreamHandler(__DIR__ . "{$ds}..{$ds}.." . $config->get("LOG_PATH"));
		$output = "%level_name%: %message% %context.user%\n";
     	$formatter = new LineFormatter($output);
		$handler->setFormatter($formatter);
        $handler->setLevel($config->get("LOG_LEVEL"));
        self::$instance->pushHandler($handler);
    }
    
    public static function debug($message, array $context = []){
		self::getLogger()->debug($message, $context);
	}

	public static function info($message, array $context = []){
		self::getLogger()->info($message, $context);
	}

	public static function notice($message, array $context = []){
		self::getLogger()->notice($message, $context);
	}

	public static function warning($message, array $context = []){
		self::getLogger()->warning($message, $context);
	}

	public static function error($message, array $context = []){
		self::getLogger()->error($message, $context);
	}

	public static function critical($message, array $context = []){
		self::getLogger()->critical($message, $context);
	}

	public static function alert($message, array $context = []){
		self::getLogger()->alert($message, $context);
	}

	public static function emergency($message, array $context = []){
		self::getLogger()->emergency($message, $context);
	}
}