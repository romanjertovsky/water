<?php

/* * * * * * * * MODULE CONFIG * * * * * * * */
$sDbTable = 'cities';
$sDbTableOrder = 'city_id';               // Сортировка и идентификация для редактирования



// Поля для формы и запроса добавления
$aFormFields =  ['city_name'];
$aFormTypes =   ['text'];
$aFormLabels =  ['Название*'];

$sSubmitCaption = 'Добавить';
$sSubmitConfirm = 'Добавить населённый пункт?';       // Вопрос при нажатии на "Сохранить"


$sSuccessMSg = 'Населённый пункт добавлен.';
$sFailMsg = 'Населённый пункт не добавлен.';
$sUrlRedirect = 'cities/';
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
        (isset($_POST['city_name'])?$_POST['city_name']:'')
    ];
$oForm->setAValues($aValues);



// Обработка формы
if (isset($_POST['go_save'])) {

    // Ошибки в процессе проверки переданных данных
    $iPostErrors = 0;


    /* * * * * * * * CHECK POST * * * * * * * */


    $iPostErrors += $this->checkEmpty($_POST['city_name'], 'Название населённого пункта не может быть пустым.');
    $iPostErrors += $this->checkExist('cities', ['city_name' => $_POST['city_name']], "Название населённого пункта \"{$_POST['city_name']}\" уже используется.");


    /* * * * * * * CHECK POST END * * * * * * */


    // Если нет ошибок - добавление строки в базу
    if($iPostErrors == 0){


        /* * * * * * * * DATA2ADD * * * * * * * */
        $aData2Add =
            [
                ['city_name'    => $_POST['city_name']]
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

$oButtonBack = new FormButton('', '', 'Назад', 'blue');
$oButtonBack->setSHref('/cities/');


Core::getView()->addAdvice('Прикрепить населённый пункт к филиалам можно после добавления. Просто найдите населённый пункт в списке и нажмите "Редактировать".');


?>

<?=$oButtonBack->makeLink()?>
<br><br>
<h5>Добавление</h5>


<?=$oForm->makeVerticalForm(2,4)?>


<br><br>
<strong></strong>