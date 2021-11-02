<?php

    $iRoleId = $this->aModuleParams[1];
    $aRoleData = Core::getDb()->fetchRow('roles', ['role_id' => $iRoleId]);


    if(empty($aRoleData)) {
        Core::getView()->addAlert("Нет роли с ID $iRoleId.", 3);
        Core::getView()->redirect(URL_HOME . 'roles/');
    }


    if (isset($_POST['go_save'])) {

        // признак ошибки в процессе проверки переданных данных
        $postError = false;

        if (empty($_POST['role_name'])) {
            $postError = true;
            Core::getView()->addAlert('Название роли не может быть пустым.', 2);
        }

        if (is_numeric($_POST['role_name'])) {
            $postError = true;
            Core::getView()->addAlert('Имя пользователя не может быть числовым.', 2);
        }

        // Проверка на совпадение имени
        if(!empty(
            Core::getDb()->fetchRow(
                    'roles',
                    [
                        ['role_name', '=', $_POST['role_name']],
                        ['role_id', '<>', $iRoleId]
                    ]
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

            $oQuery = new qbAddUpdate('roles', $aData, ['role_id' => $iRoleId]);
            $oQuery->runMakeUpdate();

            Core::getView()->addAlert('Роль сохранена.', 1);
            Core::getView()->redirect(URL_HOME . 'roles/');

        }





    } elseif (isset($_POST['go_delete'])) {

        // признак ошибки в процессе проверки переданных данных
        $postError = false;

        $aUsersWithThisRole = $this->getUsersWithRole($iRoleId);

        if(!empty($aUsersWithThisRole)) {
            $postError = true;
            Core::getView()->addAlert('Роль используется и не может быть удалёна', 3);
        }

        if (!$postError) {
            $oQuery = new qbDelete('roles', ['role_id' => $iRoleId]);
            $oQuery->runMakeDelete();
            Core::getView()->addAlert('Роль удалена.', 2);
            Core::getView()->redirect(URL_HOME . 'roles/');
        }


    }

    $oButtonBack = new FormButton('', '', 'Назад', 'blue');
    $oButtonBack->setSHref('/roles/');


    $aNames =           ['role_id',     'role_name',   'comment'];
    $aTypes =           ['readonly',    'text',         'text'];
    $aLabels =          ['ID:',         'Название роли:*',      'Описание:'];
    $aValues =          [$aRoleData['role_id'], $aRoleData['role_name'], $aRoleData['comment']];


    $oForm = new Form();
    $oForm->setANames($aNames);
    $oForm->setATypes($aTypes);
    $oForm->setALabels($aLabels);

    $oForm->setAValues($aValues);
    $oForm->setSSubmitConfirm('Сохранить роль?');


    $aUsersWithThisRole = $this->getUsersWithRole($iRoleId);

    if(empty($aUsersWithThisRole)) {
        $oForm->setBDelete(true);
        $oForm->setSDeleteConfirm("Удалить роль - {$aRoleData['role_name']}?");
    }

?>

<h5>Редактировать роль</h5>

<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>

<?php

if(!empty($aUsersWithThisRole)) {

    print "<br><br><h6>Данная роль назначена следующим пользователям:</h6>" .
    implode("<br>\n", $aUsersWithThisRole) .
    "<h6>Поэтому не может быть удалена!</h6>";

}

?>