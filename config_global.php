<?php

    // MariaDB
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_PORT', '3306');
    define('DB_NAME', 'water');
    define('DB_CHAR', 'utf8'); //попробовать поставить - utf8_general_ci

    define('DS', DIRECTORY_SEPARATOR);

    define('DIR_HOME', realpath(dirname(__FILE__)) . DS);
    define('DIR_CORE', DIR_HOME . 'core' . DS);
    define('DIR_MODS', DIR_HOME . 'modules' . DS);
    define('DIR_HTML', DIR_HOME . 'html' . DS);
    define('DIR_LIB',  DIR_HOME . 'libr' . DS);

    define('URL_HOME',
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
        "://{$_SERVER['SERVER_NAME']}/"
    );
    define('URL_ACT',
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
        "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
    );
    define('URL_CSS', URL_HOME . 'css' . '/');
    define('URL_JS',  URL_HOME . 'js' . '/');

    define('IS_DEBUG', 0);
    define('PAGE_SIZE', 10);     // элементов на страницу, при многостраничном выводе

    define('CLOSED_FOR_SERVICE', 0);     // Сайт закрыт на обновление
