<?php
namespace Database\Migrations;

use TWB\Services\DBConnectorService as DB;
use TWB\Services\Migrations\Migrate;

class CreateLoginsTable extends Migrate
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }

    public function up()
    {
        $this->getBuilder()->create('logins', function ($table) {
            $table->bigIncrements('id')->comment('固有ID');
            $table->unsignedBigInteger('account_id')->comment('アカウントID');
            $table->string('login_token')->unique()->comment('login_token');
            $table->dateTime('deadline')->comment('期限');
            $table->timestamps();

            $table->foreign('account_id', 'fk_login_account_id')
                ->references('id')
                ->on('accounts');
        });
    }

    public function down()
    {
        $this->getBuilder()->drop('logins');
    }
}