<?php
ini_set("display_errors", true);

use TWB\Services\Migrations\MyMigration;
require_once __DIR__ . '/../vendor/autoload.php';

//Migration-type
$migrateType = '';
if(array_key_exists(1, $argv)) {
    $migrateType = $argv[1];
}

$migrateStep = 1;
if(array_key_exists(2, $argv)) {
    $migrateStep = $argv[2];
}

$myMigration = new MyMigration;
$myMigration->start($migrateType, $migrateStep);
