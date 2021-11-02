<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */
    $sDbTable = 'filials';
    $sDbTableOrder = 'filial_id';               // Сортировка и идентификация для редактирования

    $aPageTableThead = ['id', 'Название'];


    // Поля для формы и запроса добавления
    $aFormFields =  ['filial_name'];
    $aFormTypes =   ['text'];
    $aFormLabels =  ['Название филиала:'];

    $sSubmitCaption = 'Добавить';
    $sSubmitConfirm = 'Добавить филиал?';       // Вопрос при нажатии на "Сохранить"

    $sSuccessMSg = 'Филиал добавлен';
    $sUrlRedirect = 'filials/';
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
        $iPostErrors += $this->checkEmpty($_POST['filial_name'], 'Название филиала не может быть пустым.');
        $iPostErrors += $this->checkNoNumeric($_POST['filial_name'], 'Название филиала не может быть числовым.');
        $iPostErrors += $this->checkExist('filials', ['filial_name' => $_POST['filial_name']], "Имя филиала \"{$_POST['filial_name']}\" уже используется.");
/* * * * * * * CHECK POST END * * * * * * */

        // Если нет ошибок - добавление строки в базу
        if($iPostErrors == 0){



/* * * * * * * * DATA2ADD * * * * * * * */
            $aData2Add =
                [
                    ['filial_name'    => $_POST['filial_name']]
                ];
/* * * * * * * DATA2ADD END * * * * * * */

            $oAddQuery = new qbAddUpdate($sDbTable, $aData2Add);
            $oAddQuery->runMakeAdd();

            Core::getView()->addAlert($sSuccessMSg, 1);
            Core::getView()->redirect(URL_HOME . $sUrlRedirect);

        }


    }

?>


<?=$oPageTable->makeTable()?>

<br>

<?=$oForm->makeHorizontalForm(5)?>
