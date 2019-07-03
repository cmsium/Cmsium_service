<?php
/**
 * Файл содержит константы, используемые для настроек приложения по умолчанию
 */

/**
 * An absolute path to apps directory
 */
define("ROOTDIR", dirname(__DIR__));

/**
 * Main settings file path
 */
define("SETTINGS_PATH", ROOTDIR."/config/config.ini");

/**
 * A list of helper function files to include
 */
define("HELPERS", [
    'main.php'
]);

define("EX_TYPES",['diff']);

define("STATUS_FETCH_TIME", 1);//ms
