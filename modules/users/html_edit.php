<?php


    $iUserId = $this->aModuleParams[1];

    $aData = Core::getDb()->fetchRow('users', ['user_id' => $iUserId]);

    if(empty($aData)) {
        Core::getView()->addAlert("Нет пользователя с ID $iUserId.", 3);
        Core::getView()->redirect(URL_HOME . 'users/');
    }



    if (isset($_POST['go_save'])) {
    // Сохранить пользователя

        $this->editGoSave($aData['user_id']);

    } elseif (isset($_POST['delete_user'])) {
    // Удалить пользователя

        $this->editGoDelete($aData['user_id']);

    } elseif (
        isset($_POST['add_role']) && is_numeric($_POST['role_id'])
    ) {
    // Добавить роль

        // TODO проверка вроде isRoleExist в базе и у пользователя, а потом уже:
        $this->addRole($iUserId, $_POST['role_id']);

    } elseif (isset($_POST['del_role']) && is_numeric($_POST['del_role'])) {
    // Удалить роль

        // TODO проверка вроде isRoleExist в базе и у пользователя, а потом уже:
        $this->delRoleAssig($_POST['del_role']);

    } elseif (
        isset($_POST['add_filial']) && is_numeric($_POST['filial_id'])
    ) {
    // Добавить филиал

        $this->addFilial($iUserId, $_POST['filial_id']);


    } elseif (isset($_POST['del_filial']) && is_numeric($_POST['del_filial'])) {
    // Удалить филиал

        $this->delFilialAssig($_POST['del_filial']);

    }


    // Кнопка - Назад
    $oButtonBack = new FormButton('', '', 'Назад', 'blue');
    $oButtonBack->setSHref('/users/');

    // Имена полей, типы полей и подписи для формы данных пользователя
    $aNames =           ['user_id',     'user_login',   'user_password',    'user_password_confirm',    'common_name',      'email',    'position'];
    $aTypes =           ['readonly',    'text',         'password',         'password',                 'text',             'text',     'text'];
    $aLabels =          ['ID:',         'Логин:',       'Пароль:',          'Подтверждение пароля:',    'ФИО:',             'Email:',   'Должность'];

    $aPlaceholders =    ['',            '',             'Введите, если хотите изменить пароль', 'Повторите, если хотите изменить пароль'];
    $aValues =          [$aData['user_id'], $aData['user_login'], '', '', $aData['common_name'], $aData['email'], $aData['position']];


    // Генерация формы данных пользователя
    $oFormCity = new Form();
    $oFormCity->setANames($aNames);
    $oFormCity->setATypes($aTypes);
    $oFormCity->setALabels($aLabels);

    $oFormCity->setAPlaceholders($aPlaceholders);
    $oFormCity->setAValues($aValues);
    $oFormCity->setBDelete(true);
    $oFormCity->setSDeleteName('delete_user');
    $oFormCity->setSSubmitConfirm('Сохранить изменения?');
    $oFormCity->setSDeleteConfirm("Удалить пользователя - {$aData['user_login']} {$aData['common_name']}?");



    // РОЛИ ПОЛЬЗОВАТЕЛЯ
    // Роли имеющиеся у пользователя - запрос
    $aRolesFields = ['assig_id', 'role_name',  'comment'];
    $aRolesThead  = ['ID', 'Роль',       'Значение'];
    $oRolesQuery = new qbSelect('view_user_to_roles', $aRolesFields, ['user_id' => $iUserId], 'role_id');
    $aRoles = $oRolesQuery->runMakeSelect();

    // Роли имеющиеся у пользователя - генерация таблицы
    $oTableRoles = new Table($aRoles);
    $oTableRoles->setAThead($aRolesThead);
    $oTableRoles->setEdit('assig_id', false, true, 'del_role');
    $oTableRoles->setSConfirmMsg('Удалить роль у пользователя?');

    // Добавление роли пользователю - форма
    $oFormRole = new Form();
    $oFormRole->setANames(['role_id']);
    $oFormRole->setATypes(['select']);
    $oFormRole->setALabels(['Добавить роль:']);

    // Запрос ролей, которые пользователю ещё не назначены
    $oDirectoryUnused = new qbDirectory(
        'roles',
        ['role_id', 'role_name', 'comment'],
        [],
        'roles_assig',
        ['user_id' => $iUserId]
    );
    $aRoles = $oDirectoryUnused->runMakeDirectory();
    $oFormRole->addASelect($aRoles);


    $oFormRole->setSSubmitName('add_role');
    $oFormRole->setSSubmitCaption('Добавить');
    $oFormRole->setSSubmitConfirm('Добавить роль пользователю?');



    // ФИЛИАЛЫ ПОЛЬЗОВАТЕЛЯ
    // Филиалы имеющиеся у пользователя - запрос
    $aFilialsFields = ['assig_id', 'filial_name'];
    $aFilialsThead  = ['ID', 'Филиал'];
    $oFilialsQuery = new qbSelect('view_user_to_filial', $aFilialsFields, ['user_id' => $iUserId], 'filial_name');
    $aFilials = $oFilialsQuery->runMakeSelect();

    // Филиалы имеющиеся у пользователя - генерация таблицы
    $oTableFilials = new Table($aFilials);
    $oTableFilials->setAThead($aFilialsThead);
    $oTableFilials->setEdit('assig_id', false, true, 'del_filial');
    $oTableFilials->setSConfirmMsg('Отключить пользователя от филиала?');

    // Добавление филиала пользователю - форма
    $oFormFilials = new Form();
    $oFormFilials->setANames(['filial_id']);
    $oFormFilials->setATypes(['select']);
    $oFormFilials->setALabels(['Добавить филиал:']);

    // Запрос ФИЛИАЛОВ, которые пользователю ещё не назначены
    $oDirectoryUnused = new qbDirectory(
        'filials',
        ['filial_id', 'filial_name'],
        [],
        'user_to_filial',
        ['user_id' => $iUserId]
    );
    $aRoles = $oDirectoryUnused->runMakeDirectory();
    $oFormFilials->addASelect($aRoles);


    $oFormFilials->setSSubmitName('add_filial');
    $oFormFilials->setSSubmitCaption('Добавить');
    $oFormFilials->setSSubmitConfirm('Подключить пользователя к филиалу?');


?>

<?=$oButtonBack->makeLink()?>
<br><br>

<h5>Данные пользователя</h5>
<br>
<?=$oFormCity->makeVerticalForm(2,4)?>
<br><br><br>

<h5>Роли пользователя</h5>
<br>
<div class="col-sm-6">
    <?=$oTableRoles->makeTable()?>
    <?=$oFormRole->makeHorizontalForm(6)?>
</div>
<br><br><br>

<h5>Филиалы пользователя</h5>
<br>
<div class="col-sm-6">
    <?=$oTableFilials->makeTable()?>
    <?=$oFormFilials->makeHorizontalForm(6)?>
</div>