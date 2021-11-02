<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'providers';
$sDbTableOrder = 'provider_id';               // Сортировка и идентификация для редактирования

$aPageTableThead = ['id', 'Поставщик', 'Номер договора', 'Дата договора'];


// Поля для формы и запроса добавления
$aFormFields =  ['provider_name', 'contract_number', 'contract_date'];
$aFormTypes =   ['text', 'text', 'date'];
$aFormLabels =  ['Название поставщика*', 'Номер договора', 'Дата договора'];

$sSubmitCaption = 'Добавить';
$sSubmitConfirm = 'Добавить поставщика?';       // Вопрос при нажатии на "Сохранить"

$sSuccessMSg = 'Поставщик добавлен.';
$sFailMsg = 'Поставщик не добавлен.';
$sUrlRedirect = 'providers/';
/* * * * * * * MODULE CONFIG END * * * * * * */


// Запрос основных данных для страницы
$oPageData = new qbSelect($sDbTable, [], [], $sDbTableOrder);
$aPageData = $oPageData->runMakeSelect();

// Главная таблица на странице
$oPageTable = new Table($aPageData);
$oPageTable->setAThead($aPageTableThead);
$oPageTable->setEdit($sDbTableOrder, true, false);


// Форма добавление записи
$oForm = new Form();
$oForm->setANames($aFormFields);
$oForm->setATypes($aFormTypes);
$oForm->setALabels($aFormLabels);
$oForm->setSSubmitCaption($sSubmitCaption);
$oForm->setSSubmitConfirm($sSubmitConfirm);


// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;


    /* * * * * * * * CHECK POST * * * * * * * */
    $iPostErrors += $this->checkEmpty($_POST['provider_name'], 'Название поставщика не может быть пустым.');
    $iPostErrors += $this->checkExist('providers', ['provider_name' => $_POST['provider_name']], "Имя поставщика \"{$_POST['provider_name']}\" уже используется.");
    if(empty($_POST['contract_date']))
        $_POST['contract_date'] = '0-0-0';
    /* * * * * * * CHECK POST END * * * * * * */

    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){

        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['provider_name'    => $_POST['provider_name']],
                ['contract_number'    => $_POST['contract_number']],
                ['contract_date'    => $_POST['contract_date']]
            ];
        /* * * * * * * DATA2ADD END * * * * * * */

        $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add);
        $oAddQuery->runMakeAdd();

        Core::getView()->addAlert($sSuccessMSg, 1);
        Core::getView()->redirect(URL_HOME . $sUrlRedirect);

    } else {
        Core::getView()->addAlert($sFailMsg, 3);
    }


}

?>


<?=$oPageTable->makeTable()?>

<br>

<?=$oForm->makeHorizontalForm(3)?>
