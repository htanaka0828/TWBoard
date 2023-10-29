<?php
namespace TWB\Services\Migrations;

use Illuminate\Events\Dispatcher;
use TWB\Services\DBConnectorService as DB;

abstract class Migrate
{
    /**
     * @var DB
     */
    protected $db;

    public function __construct(DB $db = null)
    {
        $this->db = $db ?: DB::create();
    }

    /**
     * @return DB
     */
    public function getConnector()
    {
        return $this->db;
    }

    /**
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getBuilder()
    {
        return $this->getConnector()->getConnector()->getSchemaBuilder();
    }
}