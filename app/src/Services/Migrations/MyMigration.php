<?php

namespace TWB\Services\Migrations;

use Illuminate\Events\Dispatcher;
use TWB\Utilities\StringUtility;
use TWB\Models\MigrationModel;

class MyMigration
{
    const MIGRATE_TYPE_DRY_RUN  = 'dry-run';
    const MIGRATE_TYPE_DRY_UP   = 'up';
    const MIGRATE_TYPE_DRY_DOWN = 'down';
    const MIGRATE_TYPE_LIST = [
        self::MIGRATE_TYPE_DRY_RUN,
        self::MIGRATE_TYPE_DRY_UP,
        self::MIGRATE_TYPE_DRY_DOWN,
    ];

    //000_YYMMDD_xxxxxx.php
    const CLASS_NAME_FILE_NAME_STR_LEN = 13;

    private $step = 1;

    private $batch = 0;

    private $migrationsDir = '';

    private $initialize;

    private $migrationModel;

    /**
     * MyMigration constructor.
     */
    public function __construct()
    {
        $this->migrationsDir = __DIR__ . '/../../../database/migrations';
        $this->initialize = new Initialize;
        $this->migrationModel = new MigrationModel;
    }

    /**
     * マイグレーションの開始
     * @param $type マイグレーション処理種類 : dry-run / up / down
     * @param int $step Down時のステップ数
     */
    public function start($type, $step = 1)
    {
        $this->step = $step;

        if(!self::isEnabledMigrationType($type)){
            echo '------------------------------' ."\n";
            echo 'Disable Migrate Type.' ."\n";
            echo '------------------------------' ."\n";
            exit;
        }

        //初回か判定
        if($this->isFirstTime()){
            //初回の場合
            //table - migrationsを作成
            $this->initialize->createTable();
            echo '------------------------------' ."\n";
            echo 'First time.' ."\n";
            echo 'Created migrations table.' ."\n";
            echo '------------------------------' ."\n";
        }

        if($type === self::MIGRATE_TYPE_DRY_RUN){
            $this->dryRun();
        }
        elseif($type === self::MIGRATE_TYPE_DRY_UP){
            $this->up();
        }
        elseif($type === self::MIGRATE_TYPE_DRY_DOWN){
            $this->down();
        }
    }

    /**
     * 初回判定
     * テーブル migrations 作成済みチェック
     */
    private function isFirstTime() : bool
    {
        return !$this->initialize->isCreatedMigrationsTable();
    }

    /**
     * マイグレーション : DryRun
     * 実際にマイグレーションされるファイル一覧が表示される
     */
    private function dryRun()
    {
        $migrationFiles = $this->getMigrationFiles();

        if(0 < count($migrationFiles)){
            echo '[START DRY RUN]' ."\n";
            print '------------------------------' ."\n";

            foreach ($migrationFiles as $file) {
                if(!$file['migrated']){
                    print $file['name'] ."\n";
                }
            }

            print '------------------------------' ."\n";
            echo '[END DRY RUN]' ."\n";
        }
        else{
            echo '------------------------------' ."\n";
            echo 'Non Migrate Files.' ."\n";
            echo '------------------------------' ."\n";
        }
    }

    /**
     * マイグレーション : Up
     * 実際にマイグレーションされる
     */
    private function up()
    {
        $this->batch = $this->migrationModel->getLastBatch() +1;

        $migrationFiles = $this->getMigrationFiles();

        if(0 < count($migrationFiles)){
            echo '[START UP]' ."\n";
            print '------------------------------' ."\n";

            foreach ($migrationFiles as $file) {
                if(!$file['migrated']){

                    $className = $this->getClassNameByFileName($file['name']);
                    $this->doUpMigrate($className, $file['name']);

                    print $file['name'] ."\n";
                }
            }
            print '------------------------------' ."\n";
            echo '[END UP]' ."\n";
        }
        else{
            echo '------------------------------' ."\n";
            echo 'Non Migrate Files.' ."\n";
            echo '------------------------------' ."\n";
        }
    }

    /**
     * マイグレーション : Down
     * Down処理される
     */
    private function down()
    {
        $migrations = $this->migrationModel->getRollBackListByStep($this->step);

        if(!empty($migrations)){
            echo '[START DOWN]' ."\n";

            foreach ($migrations as $migration) {

                $filePath = $this->migrationsDir .'/' .$migration->migration;
                if(file_exists($filePath)){
                    $className = $this->getClassNameByFileName($migration->migration);
                    $this->doDownMigrate($className, $migration);

                    print '------------------------------' ."\n";
                    print $migration->migration ."\n";
                    print '------------------------------' ."\n";
                }
                else{
                    print '------------------------------' ."\n";
                    print 'Not Found File ' .$migration->migration ."\n";
                    print '------------------------------' ."\n";
                }
            }
            echo '[END DOWN]' ."\n";
        }
        else{
            echo '------------------------------' ."\n";
            echo 'Non Migrate Files.' ."\n";
            echo '------------------------------' ."\n";
        }
    }

    /**
     * マイグレーションファイルの一覧を取得
     * @return array マイグレーションファイルリスト
     */
    private function getMigrationFiles()
    {
        $migrationFiles = [];

        $migrations = $this->migrationModel->getAll();

        $files = $this->getFiles();
        if(0 < count($files)){
            foreach ($files as $file) {

                $migrated = false;
                if(!empty($migrations)){
                    $migrated = $this->isMigrated($file, $migrations);
                }

                $migrationFiles[] = [
                  'name' => $file,
                  'migrated' => $migrated,
                ];
            }
        }
        return $migrationFiles;
    }

    /**
     * マイグレーション済みか判定
     * @param $file ファイル名
     * @param $migrations マイグレーションファイル名一覧
     * @return bool マイグレーション済判定
     */
    private function isMigrated($file, $migrations)
    {
        foreach ($migrations as $migrate) {
            if($migrate->migration === $file){
                return true;
            }
        }
        return false;
    }

    /**
     * マイグレーションファイル絶対パス一覧を取得する
     * @return array ファイル一覧
     */
    private function getFiles()
    {
        $files = [];

        $_files = glob($this->migrationsDir . '/' ."*.php");
        foreach ($_files as $file) {
            if(is_file($file)){

                $files[] = basename($file);
            }
        }
        return $files;
    }

    /**
     * マイグレーション処理する際のコマンドタイプが適正か判定
     * dry-run / up / down のいずれかの場合は、true
     * @param $type dry-run / up / down
     * @return bool
     */
    private static function isEnabledMigrationType($type)
    {
        if(in_array($type, self::MIGRATE_TYPE_LIST)) {
            return true;
        }
        return false;
    }

    /**
     * クラス名をスネーク型のファイル名に変換して取得
     * @param $file_name ファイル名
     * @return array|string|string[] キャメル型された文字列
     */
    private function getClassNameByFileName($file_name)
    {
        //format
        //000_YYMMDD_xxxxxx.php
        $className = substr($file_name, self::CLASS_NAME_FILE_NAME_STR_LEN);

        //拡張子を削除
        $className = substr($className, 0, -4);

        //キャメル型に変更
        return StringUtility::camelize($className);
    }

    //
    //  -- up
    //

    /**
     * マイグレーション: up を開始
     * @param $class_name クラス名
     * @param $file_name ファイル名
     */
    private function doUpMigrate($class_name, $file_name)
    {
        $this->upMigrate($class_name, $file_name);
        $this->saveMigrate($file_name);
    }

    /**
     * マイグレーション: up を開始
     * クラスファイルを実行する
     * @param $class_name クラス名
     * @param $file_name ファイル名
     */
    private function upMigrate($class_name, $file_name)
    {
        require_once $this->migrationsDir . '/' .$file_name;
        $className = "Database\Migrations\\" .$class_name;
        $class = new $className;
        $class->up();
    }

    /**
     * migrationsに保存する
     * @param $file_name ファイル名
     */
    private function saveMigrate($file_name)
    {
        $this->migrationModel->insert([
            'migration' => $file_name,
            'batch' => $this->batch
        ]);
    }

    //
    //  -- down
    //

    /**
     * マイグレーション: down を開始
     * @param $class_name クラス名
     * @param $file_name ファイル名
     */
    private function doDownMigrate($class_name, $migration)
    {
        $this->downMigrate($class_name, $migration->migration);
        $this->dropMigrate($migration);
    }

    /**
     * マイグレーション: down を開始
     * クラスファイルを実行する
     * @param $class_name クラス名
     * @param $file_name ファイル名
     */
    private function downMigrate($class_name, $file_name)
    {
        require_once $this->migrationsDir . '/' .$file_name;
        $className = "Database\Migrations\\" .$class_name;
        $class = new $className;
        $class->down();
    }

    /**
     * migrationsから、規定の行を削除する
     * @param $migration Model : Migration
     */
    private function dropMigrate($migration)
    {
        $this->migrationModel->hardDelete($migration->id);
    }
}