<?php
namespace Database\Migrations;

use TWB\Services\DBConnectorService as DB;
use TWB\Services\Migrations\Migrate;

class CreateAccountsTable extends Migrate
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }

    public function up()
    {
        $this->getBuilder()->create('accounts', function ($table) {
            $table->bigIncrements('id')->comment('固有ID');
            $table->string('name')->unique()->comment('account_name');
            $table->string('password')->comment('password');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->getBuilder()->drop('accounts');
    }
}