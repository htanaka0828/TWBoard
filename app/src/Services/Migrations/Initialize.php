<?php
namespace TWB\Services\Migrations;

use TWB\Services\DBConnectorService as DB;

class Initialize extends Migrate
{
    public function __construct(DB $db = null)
    {
        parent::__construct($db);
    }

    /**
     * テーブル作成済みか判定
     * @return bool
     */
    public function isCreatedMigrationsTable(): bool
    {
        return $this->getBuilder()->hasTable('migrations');
    }

    /**
     * テーブル作成
     */
    public function createTable()
    {
        $this->getBuilder()->create('migrations', function ($table) {
            $table->increments('id')->comment('固有ID');
            $table->string('migration', 100)->unique()->nullable()->comment('マイグレーションファイル名');
            $table->integer('batch')->unsigned()->default(0)->comment('バッチ');
            $table->timestamps();
        });
    }
}