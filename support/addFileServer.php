<?php
require_once "config/defaults.php";
require_once "config/requires_templates/requires.product.php";
require_once ROOTDIR."/lib/service_lib.php";

if (!isset($argv[1])){
    echo "Server config path missed".PHP_EOL;
    exit;
}
if (!registerFileServer($argv[1])) {
    echo "Addition failed".PHP_EOL;
    exit;
}
echo "Addition success".PHP_EOL;
