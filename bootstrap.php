<?php

    // ob_start();
    session_start();
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    date_default_timezone_set('Europe/Moscow');
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');
    header('Content-Type: text/html; charset=utf-8');

    if(IS_DEBUG === true)
        error_reporting(E_ALL);
    else
        error_reporting(0);

    require_once 'iModules.php';

    spl_autoload_register(function ($sClassName){

        // Сначала ищет в core/, затем в lib/

        $sFilePath =
            DIR_CORE .
            $sClassName .
            '.php';

        if (file_exists($sFilePath))
        {
            require_once $sFilePath;
        } else {

            $sFilePath =
                DIR_LIB .
                $sClassName .
                '.php';

            if(IS_DEBUG) Core::getView()->debug("Autoloader: $sFilePath");

            if (file_exists($sFilePath))
            {
                require_once $sFilePath;
            } else {
                die('Autoloader can\'t load class.');
            }
        }

    });


if (!function_exists('array_key_first')) {
    if(IS_DEBUG) Core::getView()->debug("Функции array_key_first() не существует, объявляем свою.");
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}