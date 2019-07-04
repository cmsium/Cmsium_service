<?php
// Bootstrapping the whole application

// Load core libraries
use App\FileServersManager;

require_once dirname(__DIR__).'/boot/defaults.php';
require_once ROOTDIR.'/core/autoload.php';

//load exceptions
foreach (glob(ROOTDIR."/app/exceptions/*.php") as $class){
    require_once $class;
}

// Load app routes
$router = new \Router\Router;
include ROOTDIR.'/app/routes.php';

// Build and load application instance
$application = \Webgear\Swoole\Application::getInstance($router);

// Prepare mysql connection data
$config = Config\ConfigManager::module('db');
$conn = [
    "host" => $config->get('servername'),
    "port" => (int)$config->get('port'),
    "database" => $config->get('dbname'),
    "user" => $config->get('username'),
    "password" => $config->get('password')
];
$application->mysql = $conn;

// Create swoole table for servers
$table = new swoole_table(100000);
$table->column('id', swoole_table::TYPE_INT, 11);
$table->column('name', swoole_table::TYPE_STRING, 64);
$table->column('ip', swoole_table::TYPE_STRING, 16);
$table->column('port', swoole_table::TYPE_INT, 6);
$table->column('url', swoole_table::TYPE_STRING, 255);
$table->column('status', swoole_table::TYPE_INT, 1);
$table->column('space', swoole_table::TYPE_FLOAT);
$table->column('workload', swoole_table::TYPE_INT, 11);
$table->create();
$application->file_servers = $table;


// Register middleware callbacks
$plumber = \Plumber\Plumber::getInstance();
$pre = $plumber->buildPipeline('webgear.pre');
$post = $plumber->buildPipeline('webgear.post');
include ROOTDIR.'/app/middleware.php';

// Load helper functions. Add file to helpers array to load it.
foreach (HELPERS as $helperFile) {
    include ROOTDIR.'/helpers/'.$helperFile;
}

// Create file manager
$manager = new App\FileServersManager($application->mysql, $application->file_servers);
$application->file_servers_manager = $manager;

// Create priority handler
$priority = new \App\FilesPriorityHandler();
$application->priority_handler = $priority;

// Save servers data from db to swoole table
$application->registerStartupCallback(function() use ($manager){
    go(function () use ($manager) {
        $manager->swooleSaveAll();
    });
});

// Servers status update callback
$application->registerStartupCallback(function () use ($manager) {
    \swoole_timer_tick(STATUS_FETCH_TIME , [$manager, 'updateStatus']);
});


