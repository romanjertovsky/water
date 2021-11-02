<?php

/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'metering_devices';

// Поля для формы и запроса добавления
$aFormFields =  ['point_id',        'device_type',  'factory_number',   'feeder_num',   'date_begin',   'initial_value',      'declared_volume'];
$aFormTypes =   ['select',          'text',         'text',             'text',         'date',         'text',               'text'];
$aFormLabels =  ['Точка поставки*', 'Тип',          'Заводской номер*', 'Номер фидера', 'Дата ввода',   'Начальное значение', 'Заявленный объём'];

$sSubmitCaption = 'Добавить';
$sSubmitConfirm = 'Добавить прибор учёта?';       // Вопрос при нажатии на "Сохранить"


$sSuccessMSg = 'Прибор учёта добавлен.';
$sUrlRedirect = 'metering_devices/';
/* * * * * * * MODULE CONFIG END * * * * * * */


// Форма добавление записи
$oForm = new Form();
$oForm->setANames($aFormFields);
$oForm->setATypes($aFormTypes);
$oForm->setALabels($aFormLabels);
$oForm->setSSubmitCaption($sSubmitCaption);
$oForm->setSSubmitConfirm($sSubmitConfirm);

$aValues =
    [
        '',
        (isset($_POST['device_type'])?$_POST['device_type']:''),
        (isset($_POST['factory_number'])?$_POST['factory_number']:''),
        (isset($_POST['feeder_num'])?$_POST['feeder_num']:''),
        (isset($_POST['date_begin'])?$_POST['date_begin']:''),
        (isset($_POST['initial_value'])?$_POST['initial_value']:''),
        (isset($_POST['declared_volume'])?$_POST['declared_volume']:'')
    ];
$oForm->setAValues($aValues);


// Список точек подключения в форме
$oPoints = new qbDirectory('supply_points', ['point_id', 'dispatch_name', 'point_name', 'point_street']);
$aPoints = $oPoints->runMakeDirectory();
$oForm->addASelect($aPoints);

// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;


    /* * * * * * * * CHECK POST * * * * * * * */

    // Проверка по id договора существует ли такой
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

    if(!empty($_POST['feeder_num']))
        $iPostErrors += $this->checkIsNumeric($_POST['feeder_num'], 'Номер фидера должен быть числовым.');

    if(empty($_POST['date_begin']))
        $_POST['date_begin'] = '0-0-0';





    /* * * * * * * CHECK POST END * * * * * * */


    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){


        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['point_id'    => $_POST['point_id']],
                ['device_type'     => $_POST['device_type']],
                ['feeder_num'     => intval($_POST['feeder_num'])],
                ['factory_number'     => $_POST['factory_number']],
                ['date_begin'   => $_POST['date_begin']],
                ['initial_value'    => floatval($_POST['initial_value'])],
                ['declared_volume'  => floatval($_POST['declared_volume'])]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */

        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add);
        $oAddQuery->runMakeAdd();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert('Прибор учёта не добавлен.', 3);
    }


}

$oButtonBack = new FormButton('', '', 'Назад', 'blue');
$oButtonBack->setSHref('/metering_devices/');

?>

<h5>Добавление прибора учёта</h5>
<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>
