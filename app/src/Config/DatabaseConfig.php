<?php
namespace TWB\Config;

use TWB\Config\ConfigInterface;

class DatabaseConfig implements ConfigInterface
{
    public static function getConfig(): Array
    {
        return [
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => getenv('DEFAULT_DB_CHARSET'),
            'collation' => getenv('DEFAULT_DB_COLLATION')
        ];
    }
}
