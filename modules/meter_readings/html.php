<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'view_meter_readings';
$sDbTable2Edit = 'meter_readings';
$sDbTableOrder = 'read_id';               // Сортировка и идентификация для редактирования

$aPageTableThead = ['id', 'Дата', 'Прибор', 'Показания'];


// Поля для формы и запроса добавления
$aFormFields =  ['read_date', 'device_id', 'read_value'];
$aFormTypes =   ['date', 'select', 'text'];
$aFormLabels =  ['Дата*', 'Прибор*', 'Показания*'];

$sSubmitCaption = 'Добавить';
$sSubmitConfirm = 'Добавить показания?';       // Вопрос при нажатии на "Сохранить"

$sSuccessMSg = 'Показания добавлены.';
$sFailMsg = 'Показания не добавлены.';
$sDeletedMSg = 'Показания удалены.';
$sUrlRedirect = 'meter_readings/';
/* * * * * * * MODULE CONFIG END * * * * * * */


// Запрос основной таблицы данных для страницы
$oPageData = new qbSelect($sDbTable, ['read_id', 'read_date', 'device_id', 'read_value']);

if(
    !array_intersect(
        ['admin'],
        Core::getUser()->getUserRoles())
    )
    $oPageData->setAWhere(['user_id', '=', Core::getUser()->getUserId()]);


$oPageData->setAOrderBy($sDbTableOrder, true);
$aPageData = $oPageData->runMakeSelectPaginated($this->iPageNo);

/* * * * * КОНВЕРТАЦИЯ ID В ЗНАЧЕНИЯ ИЗ СПРАВОЧНИКА * * * * */
$aPageData =
    $this->a_id2val(
        $aPageData,
        'device_id',
        'metering_devices',
        ['factory_number']);



// Главная таблица на странице
$oPageTable = new Table($aPageData);
$oPageTable->setAThead($aPageTableThead);
$oPageTable->setEdit($sDbTableOrder, false, true);
$oPageTable->setSConfirmMsg('Удалить строку показаний?');

// Форма добавление записи
$oForm = new Form();
$oForm->setANames($aFormFields);
$oForm->setATypes($aFormTypes);
$oForm->setALabels($aFormLabels);
$oForm->setSSubmitCaption($sSubmitCaption);
$oForm->setSSubmitConfirm($sSubmitConfirm);


$aRolesAllDevices = ['admin'];
$oDevicesDirectory = new qbDirectory();
$oDevicesDirectory->setAFields(['device_id', 'factory_number']);
if(
    empty(array_intersect(
    $aRolesAllDevices, Core::getUser()->getUserRoles()
    ))
) {
    $oDevicesDirectory->setSTable('view_users_devices');
    $oDevicesDirectory->setAWhere(['user_id' => Core::getUser()->getUserId()]);
} else {
    $oDevicesDirectory->setSTable('metering_devices');
}




$aDevices = $oDevicesDirectory->runMakeDirectory();
$oForm->addASelect($aDevices);

$aValues =
    [
        (isset($_POST['read_date'])?$_POST['read_date']:''),
        '',
        (isset($_POST['read_value'])?$_POST['read_value']:'')
    ];
$oForm->setAValues($aValues);



// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;


    /* * * * * * * * CHECK POST * * * * * * * */

    $iPostErrors += $this->checkEmpty($_POST['read_date'], 'Введите дату.');

    // TODO проверка на приборы существующие и доступные данному пользователю
    // Проверка по id прибора существует ли такой
    if(is_numeric($_POST['device_id']))  {

        $aDeviceExist = Core::getDb()->fetchRow(
            'metering_devices',
            ['device_id' => $_POST['device_id']]
        );

        if(empty($aDeviceExist)) {
            $iPostErrors++;
            Core::getView()->addAlert('Прибор учёта не существует', 2);
        }

    } else {
        $iPostErrors++;
        Core::getView()->addAlert('Выберите прибор учёта.', 2);
    }
    $_POST['read_value'] = str_replace(",",".",$_POST['read_value']);
    $iPostErrors += $this->checkEmpty($_POST['read_value'], 'Введите показания прибора учёта.');
    $iPostErrors += $this->checkIsNumeric($_POST['read_value'], 'Некорректные показания. Значение должно быть числовым.');

    /* * * * * * * CHECK POST END * * * * * * */

    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){

        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['read_date'    => $_POST['read_date']],
                ['device_id'    => $_POST['device_id']],
                ['read_value'    => $_POST['read_value']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */

        $oAddQuery = new qbAddUpdate($sDbTable2Edit, $aData2Add);
        $oAddQuery->runMakeAdd();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert($sFailMsg, 3);
    }


    // Если отправлен запрос на удаление
} elseif (isset($_POST['delete_row'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;

    // TODO проверка на то что запись создана не более сутк назад
    // Проверка существует ли запись
    if(is_numeric($_POST['delete_row']))  {

        $aReadExist = Core::getDb()->fetchRow(
            'meter_readings',
            ['read_id' => $_POST['delete_row']]
        );

        if(empty($aReadExist)) {
            $iPostErrors++;
            Core::getView()->addAlert('Запись не существует.', 2);
        }

    } else {
        $iPostErrors++;
        Core::getView()->addAlert('Невозможно удалить.', 2);
    }

    // Если нет ошибок - удаляем строку
    if($iPostErrors == 0){

        $oQuery = new qbDelete($sDbTable2Edit, [$sDbTableOrder => $_POST['delete_row']]);
        $oQuery->runMakeDelete();
        Core::getView()->addAlert($sDeletedMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    }

}


$oPaginator = new Paginator($oPageData->getCount(), $this->iPageNo);
?>

<?=$oForm->makeHorizontalForm(3)?>

<br>

<?=$oPaginator->makePaginator()?>

<?=$oPageTable->makeTable()?>

<?=$oPaginator->makePaginator()?>
