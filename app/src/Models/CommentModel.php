<?php
namespace TWB\Models;

use TWB\Services\DBConnectorService as DB;

class CommentModel extends AbstractModel
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }
}