<?php
set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__ . '/../src',
    get_include_path()
)));
spl_autoload_register(function($className) {
    if (strpos($className, 'Pkj\\Router')) {
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
        require_once $filename;
    }
});