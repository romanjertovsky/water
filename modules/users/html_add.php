<?php


if (isset($_POST['go_save'])) {


    // признак ошибки в процессе проверки переданных данных
    $postError = false;

    if (
        !empty($_POST['user_login'])
        &&
        !empty($_POST['user_password'])
        &&
        !empty($_POST['user_password_confirm'])
    ) {

        if(is_numeric($_POST['user_login'])) {
            $postError = true;
            Core::getView()->addAlert('Имя пользователя не может быть числовым', 2);
        }

        if ($_POST['user_password'] == $_POST['user_password_confirm']) {

            // Всё введено правильно, проверка на существующий логин
            if(!empty(
                Core::getDb()->fetchRow('users', ['user_login', '=', $_POST['user_login']])
            )) {
                $postError = true;
                Core::getView()->addAlert("Логин \"{$_POST['user_login']}\" уже используется. Введите другой логин.", 2);
            }

        } else {
            $postError = true;
            Core::getView()->addAlert('Пароли не совпадают.', 3);
        }

    } else {

        $postError = true;
        Core::getView()->addAlert('Ввдеите, как минимум, логин, пароль и подтверждение пароля.', 2);

    }



    if(!$postError){

        $aData =
        [
            ['user_login'        =>  $_POST['user_login']],
            ['password_md5'      =>  md5($_POST['user_password'])],
            ['common_name'       =>  $_POST['common_name']],
            ['email'             =>  $_POST['email']],
            ['position'          =>  $_POST['position']],
        ];

        $oQuery = new qbAddUpdate('users', $aData);
        $oQuery->runMakeAdd();

        Core::getView()->addAlert('Пользователь добавлен.', 1);
        Core::getView()->redirect(URL_HOME . 'users/');

    }


}




    $oButtonBack = new FormButton('', '', 'Назад', 'blue');
    $oButtonBack->setSHref('/users/');


    $aNames =           ['user_login',   'user_password',    'user_password_confirm',    'common_name',      'email',   'position'];
    $aTypes =           ['text',         'password',         'password',                 'text',             'text',    'text'];
    $aLabels =          ['Логин:*',      'Пароль:*',         'Подтверждение пароля:*',   'ФИО:',             'Email:',  'Должность'];
    $aValues =
    [
        (isset($_POST['user_login'])?$_POST['user_login']:''),
        '',
        '',
        (isset($_POST['common_name'])?$_POST['common_name']:''),
        (isset($_POST['email'])?$_POST['email']:''),
    ];


    $oForm = new Form();
    $oForm->setANames($aNames);
    $oForm->setATypes($aTypes);
    $oForm->setALabels($aLabels);
    $oForm->setAValues($aValues);
    $oForm->setSSubmitCaption('Добавить');
    $oForm->setSSubmitConfirm('Добавить нового пользователя?');


?>

<h5>Добавление нового пользователя</h5>
<?=$oButtonBack->makeLink()?>

<br><br>

<?=$oForm->makeVerticalForm(2,3)?>

<br><br>
<strong>Прикрепить пользователя к филиалам и определить роли доступа можно будет после добавления пользователя. Просто найдите пользователя в списке и нажмите "Редактировать".</strong>
