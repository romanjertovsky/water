<?php

    $aUserRow = Core::getUser()->getUserArray();
    $aUserRoles = Core::getUser()->loadRolesFromDb($aUserRow['user_id']);


    if (isset($_POST['go_save'])) {

        // признак ошибки в процессе проверки переданных данных
        $postError = false;

        // что вставляем
        $aWhat =
            [
                ['common_name' => $_POST['common_name']],
                ['email' => $_POST['email']],
            ];

        //в какую запись
        $aWhere = ['user_id' => $aUserRow['user_id']];

        // обновление пароля
        if (!empty($_POST['user_password'])) {
            if ($_POST['user_password'] == $_POST['user_password_confirm']) {
                $aWhat[] = ['password_md5' => md5($_POST['user_password'])];
            } else {
                $postError = true;
                Core::getView()->addAlert('Пароль и подтверждение пароля не совпадают.', 3);
            }
        }

        if(!$postError){
            $oQuery = new qbAddUpdate('users', $aWhat, $aWhere);
            $oQuery->runMakeUpdate();
            Core::getView()->addAlert('Ваши данные сохранёны. Можете продолжить работу.', 1);
            Core::getView()->redirect(URL_HOME . 'options/');
        }

    }


$aNames =           ['user_id',     'user_login',   'user_password',    'user_password_confirm',    'common_name',      'email',    'roles'];
$aTypes =           ['readonly',    'readonly',     'password',         'password',                 'text',             'email',    'readonly'];
$aLabels =          ['ID:',         'Логин:',       'Пароль:',          'Подтверждение пароля:',    'ФИО:',             'Email:',   'Роли:'];

$aPlaceholders =    ['',            '',             'Введите, если хотите изменить пароль', 'Повторите, если хотите изменить пароль'];
$aValues =          [$aUserRow['user_id'], $aUserRow['user_login'], '', '', $aUserRow['common_name'], $aUserRow['email'], implode(', ', $aUserRoles)];


$oForm = new Form();
$oForm->setANames($aNames);
$oForm->setATypes($aTypes);
$oForm->setALabels($aLabels);

$oForm->setAPlaceholders($aPlaceholders);
$oForm->setAValues($aValues);
$oForm->setSSubmitConfirm('Сохранить изменения?');


?>

<h5>Персональные настройки пользователя</h5>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>
