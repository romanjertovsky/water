<?php

    $oQueryRoles = new qbSelect('roles');
    $aRoles = $oQueryRoles->runMakeSelect();

    // Таблица - список ролей
    $oTable = new Table($aRoles);
    $oTable->setAThead(['id', 'Название', 'Описание']);
    $oTable->setEdit('role_id', true, false);

    // Поля для формы и для запроса на добавление
    $aFields = ['role_name', 'comment'];
    $aTypes =  ['text', 'text'];
    $aLabels = ['Название:', 'Описание:'];

    // Форма - добавить роль
    $oForm = new Form();
    $oForm->setANames($aFields);
    $oForm->setATypes($aTypes);
    $oForm->setALabels($aLabels);
    $oForm->setSSubmitCaption('Добавить');
    $oForm->setSSubmitConfirm('Добавить роль?');


    if (isset($_POST['go_save'])) {


        // признак ошибки в процессе проверки переданных данных
        $postError = false;

        if (empty($_POST['role_name'])) {
            $postError = true;
            Core::getView()->addAlert('Название роли не может быть пустым.', 2);
        }

        if (is_numeric($_POST['role_name'])) {
            $postError = true;
            Core::getView()->addAlert('Название роли не может быть числовым.', 2);
        }

        // Проверка на совпадение имени
        if(!empty(
            Core::getDb()->fetchRow(
                    'roles',
                    ['role_name' => $_POST['role_name']]
            )
        )) {
            $postError = true;
            Core::getView()->addAlert("Имя роли \"{$_POST['role_name']}\" уже используется.", 2);
        }


        if(!$postError){

            $aData =
                [
                    ['role_name'    => $_POST['role_name']],
                    ['comment'      => $_POST['comment']],
                ];

            $oQuery = new qbAddUpdate('roles', $aData);
            $oQuery->runMakeAdd();

            Core::getView()->addAlert('Роль добавлена.', 1);
            Core::getView()->redirect(URL_HOME . 'roles/');

        } else{
            Core::getView()->addAlert('Роль не добавлена.', 3);
        }


    }


?>
<h5>Роли пользователей</h5>


<?=$oTable->makeTable()?>

*[s] - системная роль, используемая в коде модулей.
<br>
<br>

<?=$oForm->makeHorizontalForm(3)?>
