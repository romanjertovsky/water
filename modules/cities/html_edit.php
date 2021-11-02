<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */

$sH5 = 'Редактировать населённый пункт';  // <h5>Заголовок</h5> вверху страницы


$sDbTable = 'cities';            // Таблица с которой работаем
$sIdField = 'city_id';                 // Идентифицирующее поле

$sSuccessMSg = 'Населённый пункт сохранён.';
$sFailMSg = 'Населённый пункт не сохранён.';
$sDeletedMSg = 'Населённый пункт удалён.';
$sUrlRedirect = 'cities/';     // гл. страница модуля



$iRowId = $this->aModuleParams[1];      // Идентификатор запрошенной записи
$aRowData = Core::getDb()->fetchRow($sDbTable, [$sIdField => $iRowId]);

// Поля для формы и запроса добавления, подписи и типы к полям
$aFormFields =  ['city_id',     'city_name'];
$aFormTypes =   ['readonly',    'text'];
$aFormLabels =  ['id',          'Название'];

$aFormValues =      [$aRowData['city_id'], $aRowData['city_name']];
$sSubmitCaption = 'Сохранить';
$sSubmitConfirm = 'Сохранить населённый пункт?';       // Вопрос при нажатии на "Сохранить"
$sDeleteConfirm = 'Удалить населённый пункт?';

/* * * * * * * MODULE CONFIG END * * * * * * */



// Проверка есть ли в таблице редактируемый id
if(empty($aRowData)) {
    Core::getView()->addAlert("Нет записи с ID $iRowId.", 3);
    Core::getView()->redirect(URL_HOME . $sUrlRedirect);
}



// Обработка форм
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;

    /* * * * * * * * CHECK POST * * * * * * * */
    $iPostErrors += $this->checkEmpty($_POST['city_name'], 'Название населённого пункта не может быть пустым.');
    $iPostErrors += $this->checkExist(
        'cities',
        [
            ['city_name', '=', $_POST['city_name']],
            ['city_id', '<>', $iRowId]
        ],
        "Имя населённого пункта \"{$_POST['city_name']}\" уже используется."
    );
    /* * * * * * * CHECK POST END * * * * * * */


    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){
        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['city_name'   => $_POST['city_name']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */


        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add, [$sIdField, '=', $iRowId]);
        $oAddQuery->runMakeUpdate();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert($sFailMSg, 3);
    }

} elseif (isset($_POST['delete_city'])) {
// Запрос на удаление

    $oQuery = new qbDelete('cities', ['city_id', '=', $iRowId]);
    $oQuery->runMakeDelete();
    Core::getView()->addAlert($sDeletedMSg, 2);
    Core::getView()->redirect(URL_HOME . $sUrlRedirect);

} elseif (
    isset($_POST['add_filial']) && is_numeric($_POST['filial_id'])
) {
// Добавить филиал

    $oQuery = new qbAddUpdate('city_to_filial', [['city_id' => $iRowId], ['filial_id' => $_POST['filial_id']]]);
    $oQuery->runMakeAdd();
    Core::getView()->addAlert('Населённый пункт присоединён к филиалу.', 1);


} elseif (isset($_POST['del_filial']) && is_numeric($_POST['del_filial'])) {
// Удалить филиал

    $oQueryDel = new qbDelete('city_to_filial', ['assig_id' => $_POST['del_filial']]);
    $oQueryDel->runMakeDelete();
    Core::getView()->addAlert('Привязка населённого пункта к филиалу удалена.', 2);

}



// Кнопка - назад
$oButtonBack = new FormButton('', '', 'Назад', 'blue');
$oButtonBack->setSHref('/' . $sUrlRedirect);


// Форма редактирования записи ГОРОД
$oFormCity = new Form();
$oFormCity->setANames($aFormFields);
$oFormCity->setATypes($aFormTypes);
$oFormCity->setALabels($aFormLabels);
$oFormCity->setAValues($aFormValues);
$oFormCity->setSSubmitCaption($sSubmitCaption);
$oFormCity->setSSubmitConfirm($sSubmitConfirm);



$oFormCity->setSSubmitConfirm('Сохранить изменения?');



// ФИЛИАЛЫ ГОРОДА - форма
// Филиалы имеющиеся у города - запрос
$aFilialsFields = ['assig_id', 'filial_name'];
$aFilialsThead  = ['ID', 'Филиал'];
$oFilialsQuery = new qbSelect('view_city_to_filial', $aFilialsFields, ['city_id' => $iRowId], 'filial_name');
$aFilials = $oFilialsQuery->runMakeSelect();

// Филиалы имеющиеся у города - генерация таблицы
$oTableFilials = new Table($aFilials);
$oTableFilials->setAThead($aFilialsThead);
$oTableFilials->setEdit('assig_id', false, true, 'del_filial');
$oTableFilials->setSConfirmMsg('Отключить населённый пункт от филиала?');



if(empty($aFilials)) {
    $oFormCity->setBDelete(true);
    $oFormCity->setSDeleteName('delete_city');
    $oFormCity->setSDeleteConfirm("Удалить населённый пункт - {$aRowData['city_name']}?");
} else {
    Core::getView()->addAdvice('Населённый пункт присоединён к одному или нескольким филиалам и не может быть удалён.');
}



// Добавление филиала городу - форма
$oFormFilials = new Form();
$oFormFilials->setANames(['filial_id']);
$oFormFilials->setATypes(['select']);
$oFormFilials->setALabels(['Добавить филиал:']);

// Запрос ФИЛИАЛОВ, которые городу ещё не назначены
$oDirectoryUnused = new qbDirectory(
    'filials',
    ['filial_id', 'filial_name'],
    [],
    'city_to_filial',
    ['city_id' => $iRowId]
);
$aRoles = $oDirectoryUnused->runMakeDirectory();
$oFormFilials->addASelect($aRoles);


$oFormFilials->setSSubmitName('add_filial');
$oFormFilials->setSSubmitCaption('Добавить');
$oFormFilials->setSSubmitConfirm('Подключить населённый пункт к филиалу?');





?>

<h5><?=$sH5?></h5>

<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oFormCity->makeVerticalForm(2,3)?>


<br><br><br>

<h5>Филиалы населённого пункта</h5>
<br>
<div class="col-sm-6">
    <?=$oTableFilials->makeTable()?>
    <?=$oFormFilials->makeHorizontalForm(6)?>
</div>