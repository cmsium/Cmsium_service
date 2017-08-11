<?php
/**
 * This file is executable
 * Use this script to undo migrations dependent on args
 */
require_once dirname(__DIR__).'/../config/config.ini';
require_once dirname(__DIR__).'/../config/defaults.php';
require_once dirname(__DIR__).'/../config/requires_templates/requires.product.php';
require_once ROOTDIR."/lib/database/Migration.php";
require_once ROOTDIR."/lib/database/MigrationHistoryHandler.php";
$history_path = Config::get('history_path');
$migrations_path = Config::get('migrations_path');

if (empty(MigrationHistoryHandler::read(ROOTDIR.$history_path))) {
    echo "Nothing to rollback";
} else {
    if ($argv[1] == 'last') {
        $migr_history = MigrationHistoryHandler::read(ROOTDIR.$history_path);
        $last_migr = end($migr_history);
        $query = file_get_contents(ROOTDIR.$migrations_path."/$last_migr/down.sql");
        DBConnection::performMigrationQuery($query);
        MigrationHistoryHandler::deleteLast(ROOTDIR.$history_path);
        echo "Migration $last_migr was rollbacked\n";
    } else {
        echo "Specific migration given. Starting rollback...\n";
        $rollback_history = array_reverse(glob(ROOTDIR.$migrations_path.'/*/down.sql'));
        foreach ($rollback_history as $dir) {
            preg_match('/.+\/(.+)\/.+\.sql$/', $dir, $matches);
            $dir_name = $matches[1];
            if ($dir_name == $argv[1]) {
                // Break the loop
                echo "Rollbacked to $dir_name successfully\n";
                break;
            } else {
                // Perform rollback
                $query = file_get_contents($dir);
                DBConnection::performMigrationQuery($query);
                echo "Version $dir_name rollbacked\n";
                MigrationHistoryHandler::deleteLast(ROOTDIR.$history_path);
            }
        }
    }
}