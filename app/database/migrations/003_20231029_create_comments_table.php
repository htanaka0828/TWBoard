<?php
namespace Database\Migrations;

use TWB\Services\DBConnectorService as DB;
use TWB\Services\Migrations\Migrate;

class CreateCommentsTable extends Migrate
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }

    public function up()
    {
        $this->getBuilder()->create('comments', function ($table) {
            $table->bigIncrements('id')->comment('固有ID');
            $table->unsignedBigInteger('account_id')->comment('アカウントID');
            $table->longText('comment')->comment('コメント');
            $table->timestamps();

            $table->foreign('account_id', 'fk_comment_account_id')
                ->references('id')
                ->on('accounts');
        });
    }

    public function down()
    {
        $this->getBuilder()->drop('comments');
    }
}