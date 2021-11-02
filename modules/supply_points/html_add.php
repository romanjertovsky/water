<?php

/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'supply_points';
$sDbTableOrder = 'point_id';               // Сортировка и идентификация для редактирования



// Поля для формы и запроса добавления
$aFormFields =  ['provider_id', 'point_name',   'city_id',       'point_street', 'point_house',  'dispatch_name',   'landmark'];
$aFormTypes =   ['select',      'text',         'select',        'text',         'text',         'text',            'text'];
$aFormLabels =  ['Поставщик*',  'Наименование*','Населённый пункт', 'Улица',     'Дом',          'Диспетчерское наименование', 'Ориентир'];

$sSubmitCaption = 'Добавить';
$sSubmitConfirm = 'Добавить точку поставки?';       // Вопрос при нажатии на "Сохранить"


$sSuccessMSg = 'Точка поставки добавлена.';
$sUrlRedirect = 'supply_points/';
/* * * * * * * MODULE CONFIG END * * * * * * */


// Форма добавление записи
$oForm = new Form();
$oForm->setANames($aFormFields);
$oForm->setATypes($aFormTypes);
$oForm->setALabels($aFormLabels);
$oForm->setSSubmitCaption($sSubmitCaption);
$oForm->setSSubmitConfirm($sSubmitConfirm);

// SELECT Поставщики
$oProvidersDirectory = new qbDirectory('providers', ['provider_id', 'provider_name']);
$aProviders = $oProvidersDirectory->runMakeDirectory();
$oForm->addASelect($aProviders);

// SELECT города
$oCitiesDirectory = new qbDirectory('cities', ['city_id', 'city_name']);
$aCities = $oCitiesDirectory->runMakeDirectory();
$oForm->addASelect($aCities);



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

        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add);
        $oAddQuery->runMakeAdd();


        /* * * * * * * * DATA2ADD * * * * * * * */
        $aPoint2City =
            [
                ['point_id'    => // TODO не костыль а костылище!!!
                    Core::getDb()->fetchArray("SELECT MAX(point_id) AS 'last_point_id' FROM supply_points;")[0]['last_point_id']
                ],
                ['city_id'     => $_POST['city_id']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */
        $oCityQuery = new qbAddUpdate('point_to_city', $aPoint2City);
        $oCityQuery->runMakeAdd();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert('Точка поставки не добавлена.', 3);
    }


}

$oButtonBack = new FormButton('', '', 'Назад', 'blue');
$oButtonBack->setSHref('/supply_points/');


Core::getView()->addAdvice('После добавления точки найдите её в списке, нажмите "Редактировать" и добавьте одного или нескольких операторов, которые будут с ней работать .');

?>

<h5>Добавление точки поставки</h5>
<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>
