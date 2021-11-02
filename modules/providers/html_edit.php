<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */

$sH5 = 'Редактировать поставщика';  // <h5>Заголовок</h5> вверху страницы

$sDbTable = 'providers';          // Таблица с которой работаем
$sIdField = 'provider_id';        // Идентифицирующее поле
$sSuccessMSg = 'Поставщик сохранён';
$sDeletedMSg = 'Поставщик удалён';
$sUrlRedirect = 'providers/';     // Редирект на гл страницу модуля

$iRowId = $this->aModuleParams[1];      // Идентификатор запрошенной записи
$aRowData = Core::getDb()->fetchRow($sDbTable, [$sIdField => $iRowId]);

// Поля для формы и запроса добавления
$aFormFields =  ['provider_id', 'provider_name', 'contract_number', 'contract_date'];
$aFormTypes =   ['readonly', 'text', 'text', 'date'];
$aFormLabels =  ['id', 'Название поставщика*', 'Номер договора', 'Дата договора'];


$aFormValues =      [$aRowData['provider_id'], $aRowData['provider_name'], $aRowData['contract_number'], $aRowData['contract_date']];
$sSubmitCaption = 'Сохранить';
$sSubmitConfirm = 'Сохранить поставщика?';       // Вопрос при нажатии на "Сохранить"
$sDeleteConfirm = 'Удалить поставщика?';


// TODO Проверка - запретить удалять, если данные использованы
$sUsedTable = '';               // Таблица, где эти данные используются
$sUsedField = '';               // Поле, где можно найти запись по $iRowId

/* * * * * * * MODULE CONFIG END * * * * * * */



// Проверка есть ли в таблице запрошенный id
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


// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;



    /* * * * * * * * CHECK POST * * * * * * * */
    $iPostErrors += $this->checkEmpty($_POST['provider_name'], 'Название поставщика не может быть пустым.');
    $iPostErrors += $this->checkExist(
            'providers',
            [
                    ['provider_name' => $_POST['provider_name']],
                    ['provider_id', '<>', $iRowId]
            ],
            "Имя поставщика \"{$_POST['provider_name']}\" уже используется."
    );

    if(empty($_POST['contract_date']))
        $_POST['contract_date'] = '0-0-0';
    /* * * * * * * CHECK POST END * * * * * * */



    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){
        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['provider_id'    => $_POST['provider_id']],
                ['provider_name'    => $_POST['provider_name']],
                ['contract_number'    => $_POST['contract_number']],
                ['contract_date'    => $_POST['contract_date']],
            ];
        /* * * * * * * DATA2ADD END * * * * * * */

        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add, [$sIdField, '=', $iRowId]);
        $oAddQuery->runMakeUpdate();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    }



    // Если отправлен запрос на удаление
} elseif (isset($_POST['go_delete'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;

    $aPointsWithThisProvider = $this->getProvidersWithRole($iRowId);

    if(!empty($aPointsWithThisProvider)) {
        $iPostErrors++;
        Core::getView()->addAlert('Поставщик используется и не может быть удалён.', 3);
    }

    // Если нет ошибок - удаляем строку
    if($iPostErrors == 0){

        $oQuery = new qbDelete($sDbTable, [$sIdField => $iRowId]);
        $oQuery->runMakeDelete();
        Core::getView()->addAlert($sDeletedMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    }

}

$aPointsWithThisProvider = $this->getProvidersWithRole($iRowId);


if(empty($aPointsWithThisProvider)) {

    $oForm->setBDelete(true);
    $oForm->setSDeleteConfirm("$sDeleteConfirm (id={$aRowData[$sIdField]})");

}
?>

    <h5><?=$sH5?></h5>

<?=$oButtonBack->makeLink()?>

    <br><br>

<?=$oForm->makeVerticalForm(2,3)?>


<?php


if(!empty($aPointsWithThisProvider)) {

    print "<br><br><h6>К данному поставщику присоеденины следующие точки поставки:</h6>" .
        implode("<br>\n", $aPointsWithThisProvider) .
        "<h6>Поэтому он не может быть удалён!</h6>";

}

?>
