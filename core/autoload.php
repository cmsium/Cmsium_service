<?php

function loadRecursive($path, $name) {
    $items = glob($path.DIRECTORY_SEPARATOR."*");

    foreach($items as $item) {
    $isPhp = (isset(pathinfo($item)["extension"]) && pathinfo($item)["extension"] === "php");

    if (is_file($item) && $isPhp && (basename($item) == "$name.php")) {
          include $item;
        } elseif (is_dir($item)) {
          loadRecursive($item, $name);
        }
    }
}

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/validation', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/router', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/config', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/http_server', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/plumber', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/utils/openapi', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/utils/migrator', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/database', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/config', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/validation', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/http_server', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/webgear', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/presenter', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/transaction', $className);
});

spl_autoload_register(function ($className) {
    $classNameArray = explode('\\', $className);
    $className = array_pop($classNameArray);
    loadRecursive('/home/nick/cmsium/Cmsium_service/core/lib/queue', $className);
});

