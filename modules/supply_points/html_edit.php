<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */

$sH5 = 'Редактировать точку поставки';  // <h5>Заголовок</h5> вверху страницы

$sDbTable = 'supply_points';          // Таблица с которой работаем
$sIdField = 'point_id';        // Идентифицирующее поле
$sSuccessMSg = 'Точка поставки сохранёна';
$sDeletedMSg = 'Точка поставки удалена';
$sUrlRedirect = 'supply_points/';     // гл. страница модуля

$iRowId = $this->aModuleParams[1];      // Идентификатор запрошенной записи
$aRowData = Core::getDb()->fetchRow($sDbTable, [$sIdField => $iRowId]);



// Поля для формы и запроса добавления, подписи и типы к полям
$aFormFields =  ['point_id',    'provider_id', 'point_name',   'city_id',       'point_street', 'point_house',  'dispatch_name',   'landmark'];
$aFormTypes =   ['readonly',    'select',      'text',         'select',        'text',         'text',         'text',            'text'];
$aFormLabels =  ['id',          'Поставщик',   'Наименование', 'Населённый пункт', 'Улица',     'Дом',          'Диспетчерское наименование', 'Ориентир'];

$aFormValues =      [$iRowId, $aRowData['provider_id'], $aRowData['point_name'],'',$aRowData['point_street'],$aRowData['point_house'],$aRowData['dispatch_name'],$aRowData['landmark']];
$sSubmitCaption = 'Сохранить';
$sSubmitConfirm = 'Сохранить точку поставки?';       // Вопрос при нажатии на "Сохранить"
$sDeleteConfirm = 'Удалить точку поставки?';


/* * * * * * * MODULE CONFIG END * * * * * * */



// Проверка есть ли в таблице редактируемый id
if(empty($aRowData)) {
    Core::getView()->addAlert("Нет записи с ID $iRowId.", 3);
    Core::getView()->redirect(URL_HOME . $sUrlRedirect);
}

// TODO проверка - использована ли данная запись где-нибудь
$aRowUsed = [];

// Кнопка - назад
$oButtonBack = new FormButton('', '', 'Назад', 'blue');
$oButtonBack->setSHref('/' . $sUrlRedirect);


// Форма редактирования записи
$oForm = new Form();
$oForm->setANames($aFormFields);
$oForm->setATypes($aFormTypes);
$oForm->setALabels($aFormLabels);
$oForm->setAValues($aFormValues);
$oForm->setSSubmitCaption($sSubmitCaption);
$oForm->setSSubmitConfirm($sSubmitConfirm);
// Список поставщиков в форме
$oProvidersDirectory = new qbDirectory('providers', ['provider_id', 'provider_name']);
$aRoles = $oProvidersDirectory->runMakeDirectory();
$oForm->addASelect($aRoles);
// Выбранный поставщик
$iProviderSelected = Core::getDb()->fetchRow('supply_points', ['point_id' => $iRowId]);
if(isset($iProviderSelected['provider_id']))
    $oForm->addSelected($iProviderSelected['provider_id']);

// SELECT города
$oCitiesDirectory = new qbDirectory('cities', ['city_id', 'city_name']);
$aCities = $oCitiesDirectory->runMakeDirectory();
$oForm->addASelect($aCities);
// Выбранный город
$iCitySelected = Core::getDb()->fetchRow('view_point_to_city', ['point_id' => $iRowId]);
if(isset($iCitySelected['city_id']))
    $oForm->addSelected($iCitySelected['city_id']);


// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;


    /* * * * * * * * CHECK POST * * * * * * * */
    // Проверка по id договора существует ли такой
    if(is_numeric($_POST['provider_id']))  {

        $aProviderExist = Core::getDb()->fetchRow(
            'providers',
            ['provider_id' => $_POST['provider_id']]
        );

        if(empty($aProviderExist)) {
            $iPostErrors++;
            Core::getView()->addAlert('Договор не существует', 2);
        }

    } else {
        $iPostErrors++;
        Core::getView()->addAlert('Не выбран поставщик.', 2);
    }

    if(is_numeric($_POST['city_id']))  {

        $aCityExist = Core::getDb()->fetchRow(
            'cities',
            ['city_id' => $_POST['city_id']]
        );

        if(empty($aCityExist)) {
            $iPostErrors++;
            Core::getView()->addAlert('Населённый пункт не существует', 2);
        }

    } else {
        $iPostErrors++;
        Core::getView()->addAlert('Не выбран населённый пункт.', 2);
    }

    $iPostErrors += $this->checkEmpty($_POST['point_name'], 'Наименование точки поставки не может быть пустым.');


    /* * * * * * * CHECK POST END * * * * * * */

    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){
        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['provider_id'    => $_POST['provider_id']],
                ['point_name'     => $_POST['point_name']],
                ['point_street'   => $_POST['point_street']],
                ['point_house'    => $_POST['point_house']],
                ['dispatch_name'  => $_POST['dispatch_name']],
                ['landmark'       => $_POST['landmark']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */

        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add, [$sIdField, '=', $iRowId]);
        $oAddQuery->runMakeUpdate();


        /* * * * * * * * DATA2ADD * * * * * * * */
        $aPoint2City =
            [
                ['city_id'     => $_POST['city_id']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */
        $oCityQuery = new qbAddUpdate('point_to_city', $aPoint2City, [$sIdField, '=', $iRowId]);
        $oCityQuery->runMakeUpdate();


        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert('Точка поставки не сохранена.', 3);
    }



    // Если отправлен запрос на удаление
} elseif (isset($_POST['go_delete'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;

    // Проверка, что запись нигде не задействована
    $aRecordsUsed = $this->getRecordsUsed('metering_devices', ['device_id', 'factory_number'], 'point_id', $iRowId);
    if(!empty($aRecordsUsed)) {
        $iPostErrors++;
        Core::getView()->addAlert('Точка поставки задействована и не может быть удалена.', 3);
    }

    // Если нет ошибок - удаляем строку
    if($iPostErrors == 0){

        $oQuery = new qbDelete($sDbTable, [$sIdField => $iRowId]);
        $oQuery->runMakeDelete();
        Core::getView()->addAlert($sDeletedMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    }

}  elseif (isset($_POST['add_operator']) && is_numeric($_POST['user_id'])) {
    // Добавляем пользователя-оператора

    $oQuery = new qbAddUpdate('user_to_point', [['user_id' => $_POST['user_id']], ['point_id' => $iRowId]]);
    $oQuery->runMakeAdd();
    Core::getView()->addAlert('Пользователь присоединён к точке поставки.', 1);

} elseif (isset($_POST['del_operator']) && is_numeric($_POST['del_operator'])) {

    $oQueryDel = new qbDelete('user_to_point', ['assig_id' => $_POST['del_operator']]);
    $oQueryDel->runMakeDelete();
    Core::getView()->addAlert('Привязка пользователя к точке поставки удалена.', 2);

}


// Проверка ещё раз, что запись нигде не задействована
$aRecordsUsed = $this->getRecordsUsed('metering_devices', ['device_id', 'factory_number'], 'point_id', $iRowId);
if(empty($aRecordsUsed)) {
    $oForm->setBDelete(true);
    $oForm->setSDeleteConfirm("$sDeleteConfirm (id={$aRowData[$sIdField]})");
}



if(!empty($aRecordsUsed)) {
    Core::getView()->addAdvice(
        '<h6>К данной точке поставки присоеденины следующие приборы учёта (id, зав.№):</h6>' .
        implode("<br>\n", $aRecordsUsed) .
        "<h6>Поэтому она не может быть удалена!</h6>"
    );
}



// ОПЕРАТОРЫ
// Операторы, которые уже подключены
$aOperatorsFields = ['assig_id', 'user_login'];
$aOperatorsThead  = ['ID', 'Login'];
$oOperatorsQuery = new qbSelect('view_user_to_point', $aOperatorsFields, ['point_id' => $iRowId]);
$aOperators = $oOperatorsQuery->runMakeSelect();

if(empty($aOperators))
    Core::getView()->addAdvice('К данной точке поставки не присоеденино ни одного оператора.');

// Операторы, которые подключены - генерация таблицы
$oTableOperators = new Table($aOperators);
$oTableOperators->setAThead($aOperatorsThead);
$oTableOperators->setEdit('assig_id', false, true, 'del_operator');
$oTableOperators->setSConfirmMsg('Отключить пользователя от точки поставки?');

// Добавление оператора - форма
$oFormOperators = new Form();
$oFormOperators->setANames(['user_id']);
$oFormOperators->setATypes(['select']);
$oFormOperators->setALabels(['Добавить оператора']);

// Список всех пользователей, которые данной точке ещё не назначены
$oDirectoryUsers = new qbDirectory(
    'users',
    ['user_id', 'user_login'],
    [],
    'user_to_point',
    ['point_id' => $iRowId]
);
$aDirectoryUsers = $oDirectoryUsers->runMakeDirectory();
$oFormOperators->addASelect($aDirectoryUsers);

$oFormOperators->setSSubmitName('add_operator');
$oFormOperators->setSSubmitCaption('Добавить');
$oFormOperators->setSSubmitConfirm('Добавить оператора к точке поставки?');


?>

    <h5><?=$sH5?></h5>

<?=$oButtonBack->makeLink()?>

    <br><br>

<?=$oForm->makeVerticalForm(2,3)?>
<br><br><br>


<h5>Операторы точки поставки</h5>
<br>
<div class="col-sm-6">
    <?=$oTableOperators->makeTable()?>
    <?=$oFormOperators->makeHorizontalForm(6)?>
</div>