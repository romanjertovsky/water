<?php


     /*
     4.1 версия.
     Теперь все необходимые элементы страниц, а так-же SQL запросы
     имеют свой класс и генерируются при помощи билдеров и конструкторов.
     Внесены исправления для совместимости с PHP 7.4, исправлены ошибки.
     */


    define('TIME_START', microtime(true));

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    require_once 'config_global.php';
    require_once 'bootstrap.php';
    require_once 'Core.php';

    if(IS_DEBUG) Core::getView()->debug("Включен режим debug. Начало работы скрипта.");

    Core::getController()->appStart();


    /*
     * Как добавить модуль в систему и в меню:
     *
     * 1. Добавить каталог в modules/ и в этот каталог php файл с тем-же именем
     * 2. В файл класс модуля с тем же именем, extends ModuleCommon implements iModules
     * 3. Переопределить нужные методы из ModuleCommon
     * 4. Добавить в файле html/menu.php в массив $aMenuItems
     *
     */
