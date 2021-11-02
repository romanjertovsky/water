<?php


class login implements iModules
{


    public function getModuleRoles(): array
    {
        return ['*'];
    }


    public function getTitle(): string
    {
        return 'Система учёта электроэнергии.';
    }

    public function getContent(): string
    {
        ob_start();
        include "login.html";
        return ob_get_clean();
    }


    public function moduleInit($aModuleParams)
    {
        if(IS_DEBUG)
            Core::getView()->debug("login->moduleStart()<br>");


        if (isset($_POST['go_login'])) {
            if(
                (isset($_POST['user_login']) && $_POST['user_login'] != '')
                &&
                (isset($_POST['password']) && $_POST['password'] != '')
            ) {

                $user_login = $_POST['user_login'];
                $password_md5 = md5($_POST['password']);

                $aTempUser = Core::getDb()->fetchRow(
                    'users',
                    [
                        ['user_login', '=', $user_login],
                        ['password_md5', '=', $password_md5]
                ]);


                if (empty($aTempUser)) {
                    Core::getView()->addAlert('Неверный логин или пароль!', 3);
                    Core::getView()->redirect('/');
                } else {
                    $_SESSION['user_login'] = $aTempUser['user_login'];
                    Core::getView()->redirect('/');
                }

            } else {
                Core::getView()->addAlert('Введите логин и пароль.', 2);
                Core::getView()->redirect('/');
            }
        }

    }


}