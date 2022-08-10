<?php
spl_autoload_register(function ($class) {
    $file = explode('\\', $class);
    $file_name = array_pop($file) . '.php';

    $file = implode(DIRECTORY_SEPARATOR, $file);
    $file = strtolower($file) . DIRECTORY_SEPARATOR . $file_name;

    if (file_exists($file)) {
        require $file;
        return true;
    } else {
        echo $file . ' não encontrado!';
        exit();
    }
    return false;
});