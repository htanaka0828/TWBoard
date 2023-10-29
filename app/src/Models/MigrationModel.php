<?php
namespace TWB\Models;

use TWB\Services\DBConnectorService as DB;

class MigrationModel extends AbstractModel
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }

    public function getAll()
    {
        return $this->getBuilder()->orderBy('created_at', 'desc')->get();
    }

    public function getLastBatch()
    {
        if(0 < $this->getBuilder()->orderBy('batch', 'desc')->count()){
            return $this->getBuilder()->orderBy('batch', 'desc')->first()->batch;
        }
        return 0;
    }

    public function getRollBackListByStep($step = 0)
    {
        $minBatch = $this->getLastBatch() - $step;
        return $this->getBuilder()
            ->where('batch', '>', $minBatch)
            ->orderBy('batch', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }
}