<?php
namespace TWB\Services;

use TWB\Config\DatabaseConfig;

class ConfigService
{
    /**
     * @return array
     */
    public function getDatabaseConfigure()
    {
        return DatabaseConfig::getConfig();
    }

}