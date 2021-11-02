<?php


$aAllModsObjects = [];      // объекты всех модулей, только для вызова метода getModuleRoles()
$aAllModsRoles = [];        // массив всех ролей всех молулей


$aMenuItems = [
    //'Имя модуля'    => [Выводим?, 'Подпись'],
    'default_module'    => [true, 'Главная'],

    '#'    => [true, '|'],

    'meter_readings'    => [false, 'Показания'],
    'metering_devices'  => [false, 'Приборы учёта'],

    '##'    => [true, '|'],

    'supply_points'     => [false, 'Точки поставки'],
    'providers'         => [false, 'Поставщики'],
    'cities'           => [false, 'Города'],
    'filials'           => [false, 'Филиалы'],

    '###'    => [true, '|'],

    'users'             => [false, 'Пользователи'],
    'roles'             => [false, 'Роли'],

    '####'    => [true, '|'],

    'options'           => [false, 'Настройки'],
    'test'              => [false, 'Test'],
    'logout'            => [true, 'Выход']
];


//TODO далее следует дичь дичайшая для меню -----
$aModsDirs = scandir(DIR_MODS);

// создаём объекты из каждого модуля
foreach ($aModsDirs as $val) {
    $sModsFile = DIR_MODS . "/$val/$val.php";
    if(file_exists($sModsFile)) {
        require_once $sModsFile;
        $aAllModsObjects[$val] = new $val;
    }
}

// запрашиваем права у каждого модуля
foreach ($aAllModsObjects as $key => $val) {
    $aAllModsRoles[$key] = $val->getModuleRoles();
}

// Вывод списка всех модулей со всеми их ролями
// print_r($aAllModsRoles);

unset($aAllModsObjects, $val);
// конец дичи для меню, массив $aAllModsRoles - заполнен -----


//Перебираем $aMenuItems, и заменяем в нём false на true, при наличии прав доступа
foreach ($aMenuItems as $key => $val) {
    if(array_key_exists($key, $aAllModsRoles))
    {

        $aIntersectedRoles = array_intersect($aAllModsRoles[$key], Core::getUser()->getUserRoles());

        if(
            in_array('*', $aAllModsRoles[$key])   // есть * в ролях модуля
            ||
            !empty($aIntersectedRoles)         // не пусто в массиве совпадающих прав
        ) {
            $aMenuItems[$key][0] = true;
        }

    }
}



?>

<div class="col-md-12 main-menu">
    <h5>Личный кабинет
        <?=Core::getUser()->getUserArray()['user_login']?>
        (<?=Core::getUser()->getUserArray()['common_name']?>)
    </h5>
    <ul class="nav nav-pills">
<?php


    foreach ($aMenuItems as $key => $val) {
        if ($val[0])
            print
"        <li class=\"nav-item\">
            <a class=\"nav-link" . ($key == Core::getController()->getSModName()?' active':'') . "\" href=\"/{$key}/\">{$val[1]}</a>
        </li> \n";
    }

?>
    </ul>
</div>