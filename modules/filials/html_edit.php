<?php


/* * * * * * * * MODULE CONFIG * * * * * * * */

    $sH5 = 'Редактировать филиал';  // <h5>Заголовок</h5> вверху страницы

    $sDbTable = 'filials';          // Таблица с которой работаем
    $sIdField = 'filial_id';        // Идентифицирующее поле
    $sSuccessMSg = 'Филиал сохранён';
    $sDeletedMSg = 'Филиал удалён';
    $sUrlRedirect = 'filials/';     // Редирект на гл страницу модуля

    $iRowId = $this->aModuleParams[1];      // Идентификатор запрошенной записи
    $aRowData = Core::getDb()->fetchRow($sDbTable, [$sIdField => $iRowId]);

    // Поля для формы и запроса добавления, подписи и типы к полям
    $aFormFields =      ['filial_id',   'filial_name'];
    $aFormTypes =       ['readonly',    'text'];
    $aFormLabels =      ['ID:',         'Название филиала:*'];

    $aFormValues =      [$aRowData['filial_id'], $aRowData['filial_name']];
    $sSubmitCaption = 'Сохранить';
    $sSubmitConfirm = 'Сохранить филиал?';       // Вопрос при нажатии на "Сохранить"
    $sDeleteConfirm = 'Удалить филиал?';


    // TODO Проверка - запретить удалять, если данные использованы
    $sUsedTable = '';               // Таблица, где эти данные используются
    $sUsedField = '';               // Поле, где можно найти запись по $iRowId

/* * * * * * * MODULE CONFIG END * * * * * * */



    // Проверка есть ли в таблице запрошенный id
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
    if(empty($aRowUsed)) {
        $oForm->setBDelete(true);
        $oForm->setSDeleteConfirm("$sDeleteConfirm (id={$aRowData[$sIdField]})");
    }


    // Обработка формы
    if (isset($_POST['go_save'])) {

        // Ошибки в процессе проверки переданных данных
        $iPostErrors = 0;



/* * * * * * * * CHECK POST * * * * * * * */
        $iPostErrors += $this->checkEmpty($_POST['filial_name'], 'Название филиала не может быть пустым.');
        $iPostErrors += $this->checkNoNumeric($_POST['filial_name'], 'Название филиала не может быть числовым.');
        $iPostErrors += $this->checkExist(
            'filials',
            [
                ['filial_name', '=', $_POST['filial_name']],
                ['filial_id', '<>', $iRowId]
            ],
            "Имя филиала \"{$_POST['filial_name']}\" уже используется."
        );
/* * * * * * * CHECK POST END * * * * * * */



        // Если нет ошибок - добавление строки в базу
        if($iPostErrors == 0){
/* * * * * * * * DATA2ADD * * * * * * * */
            $aData2Add =
                [
                    ['filial_name'    => $_POST['filial_name']]
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

        // TODO проверка использована ли строка или нет!

        // Если нет ошибок - удаляем строку
        if($iPostErrors == 0){

            $oQuery = new qbDelete($sDbTable, [$sIdField => $iRowId]);
            $oQuery->runMakeDelete();
            Core::getView()->addAlert($sDeletedMSg, 1);
            Core::getView()->redirect(URL_HOME . $sUrlRedirect);

        }

    }

?>

<h5><?=$sH5?></h5>

<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>