<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */

$sH5 = 'Редактировать прибор учёта';  // <h5>Заголовок</h5> вверху страницы

$sDbTable = 'metering_devices';          // Таблица с которой работаем
$sIdField = 'device_id';        // Идентифицирующее поле
$sSuccessMSg = 'Прибор учёта сохранён.';
$sDeletedMSg = 'Прибор учёта удалён.';
$sUrlRedirect = 'metering_devices/';     // гл. страница модуля


$iRowId = $this->aModuleParams[1];      // Идентификатор запрошенной записи
$aRowData = Core::getDb()->fetchRow($sDbTable, [$sIdField => $iRowId]);

// TODO сделать инициализацию $aRecordsUsed во всех скриптах "edit" в начале, как здесь, и убрать два раза в конце
// Если это справочник, то находим где эта запись уже использована
$aRecordsUsed = $this->getRecordsUsed('meter_readings', ['read_id', 'read_date', 'read_value'], 'device_id', $iRowId);

// Поля для формы и запроса добавления
$aFormFields =  ['device_id',   'point_id',        'device_type',  'factory_number',   'feeder_num',    'date_begin',    'date_end',    'initial_value',        'declared_volume'];
$aFormTypes =   ['readonly',    'select',          'text',         'text',             'text',          'date',          'date',        'text',                 'text'];
$aFormLabels =  ['id',          'Точка поставки*', 'Тип',          'Заводской номер*', 'Номер фидера',  'Дата ввода',    'Дата вывода', 'Начальное значение',   'Заявленный объём'];


$aFormValues =      [$aRowData['device_id'], 0, $aRowData['device_type'],$aRowData['factory_number'],$aRowData['feeder_num'],$aRowData['date_begin'],$aRowData['date_end'],$aRowData['initial_value'],$aRowData['declared_volume']];
$sSubmitCaption = 'Сохранить';
$sSubmitConfirm = 'Сохранить прибор учёта?';       // Вопрос при нажатии на "Сохранить"
$sDeleteConfirm = 'Удалить прибор учёта?';


/* * * * * * * MODULE CONFIG END * * * * * * */



// Проверка есть ли в таблице редактируемый id
if(empty($aRowData)) {
    Core::getView()->addAlert("Нет записи с ID $iRowId.", 3);
    Core::getView()->redirect(URL_HOME . $sUrlRedirect);
}


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
// Список точек подключения в форме
$oPoints = new qbDirectory('supply_points', ['point_id', 'dispatch_name', 'point_name', 'point_street']);
$aPoints = $oPoints->runMakeDirectory();
$oForm->addASelect($aPoints);
// Выбранная точка поставки
$iSPSelected = Core::getDb()->fetchRow('metering_devices', ['device_id' => $iRowId]);
if(isset($iSPSelected['point_id']))
    $oForm->addSelected($iSPSelected['point_id']);


// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;



    /* * * * * * * * CHECK POST * * * * * * * */
    // Проверка по id точки поставки существует ли такая
    if(is_numeric($_POST['point_id']))  {

        $aProviderExist = Core::getDb()->fetchRow(
            'supply_points',
            ['point_id' => $_POST['point_id']]
        );

        if(empty($aProviderExist)) {
            $iPostErrors++;
            Core::getView()->addAlert('Точка поставки не существует', 2);
        }

    } else {
        $iPostErrors++;
        Core::getView()->addAlert('Не выбрана точка поставки.', 2);
    }

    $iPostErrors += $this->checkEmpty($_POST['factory_number'], 'Заводской номер не может быть пустым.');
    $iPostErrors += $this->checkIsNumeric($_POST['feeder_num'], 'Номер фидера должен быть числовым.');

    if(empty($_POST['date_begin']))
        $_POST['date_begin'] = '0-0-0';

    if(empty($_POST['date_end']))
        $_POST['date_end'] = '0-0-0';

    /* * * * * * * CHECK POST END * * * * * * */



    $aFormFields =  ['device_id',   'point_id',     'device_type',  'factory_number',   'feeder_num',   'date_begin',    'date_end',   'initial_value',
        'declared_volume'];

    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){
        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['point_id'         => $_POST['point_id']],
                ['device_type'      => $_POST['device_type']],
                ['factory_number'   => $_POST['factory_number']],
                ['feeder_num'       => $_POST['feeder_num']],
                ['date_begin'       => $_POST['date_begin']],
                ['date_end'         => $_POST['date_end']],
                ['initial_value'    => floatval($_POST['initial_value'])],
                ['declared_volume'  => floatval($_POST['declared_volume'])]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */


        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add, [$sIdField, '=', $iRowId]);
        $oAddQuery->runMakeUpdate();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert('Прибор учёта не сохранён.', 3);
    }



    // Если отправлен запрос на удаление
} elseif (isset($_POST['go_delete'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;

    // Проверка, что запись нигде не задействована
    if(!empty($aRecordsUsed)) {
        $iPostErrors++;
        Core::getView()->addAlert('Прибор учёта задействован и не может быть удален.', 3);
    }

    // Если нет ошибок - удаляем строку
    if($iPostErrors == 0){

        $oQuery = new qbDelete($sDbTable, [$sIdField => $iRowId]);
        $oQuery->runMakeDelete();
        Core::getView()->addAlert($sDeletedMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    }

}

if(empty($aRecordsUsed)) {

    $oForm->setBDelete(true);
    $oForm->setSDeleteConfirm("$sDeleteConfirm (id={$aRowData[$sIdField]})");

} else {

    Core::getView()->addAdvice(
        "<h6>Данный прибор учёта имеет след. записи показаний: (id, дата, показания):</h6>" .
        implode("<br>\n", $aRecordsUsed) .
        "<h6>Поэтому не может быть удален!</h6>"
    );

}



?>

    <h5><?=$sH5?></h5>

<?=$oButtonBack->makeLink()?>

    <br><br>

<?=$oForm->makeVerticalForm(2,3)?>
