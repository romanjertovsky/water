<?php


class users extends ModuleCommon implements iModules
{


    public function getModuleRoles(): array
    {
        return ['admin'];
    }


    public function getTitle(): string
    {
        return 'Пользователи';
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }


    protected function editGoSave(int $iUserId) {

        // признак ошибки в процессе проверки переданных данных
        $postError = false;

        // проверка на существующий логин
        $aTempUserRow = Core::getDb()->fetchRow('users', ['user_login' => $_POST['user_login']]);
        if(
            !empty($aTempUserRow)
            &&
            $aTempUserRow['user_id'] != $iUserId
        ) {
            $postError = true;
            Core::getView()->addAlert("Логин {$_POST['user_login']} уже используется.", 3);
        }

        // что вставляем
        $aWhat =
            [
                ['user_login'    => $_POST['user_login']],
                ['common_name'   => $_POST['common_name']],
                ['email'         => $_POST['email']],
                ['position'      => $_POST['position']],
            ];

        // в какую запись
        $aWhere = ['user_id' => $iUserId];


        // обновление пароля
        if (!empty($_POST['user_password'])) {
            if ($_POST['user_password'] == $_POST['user_password_confirm']) {

                $aWhat[] = ['password_md5' => md5($_POST['user_password'])];

            } else {
                $postError = true;
                Core::getView()->addAlert('Пароли не совпадают.', 3);
            }
        }


        if(!$postError){
            $oQuery = new qbAddUpdate('users', $aWhat, $aWhere);
            $oQuery->runMakeUpdate();
            Core::getView()->addAlert('Пользователь сохранён.', 1);
            Core::getView()->redirect(URL_HOME . 'users/');
        }

    }

    protected function editGoDelete(int $iUserId) {
        /* TODO сделать проверку на кол-во ВСЕХ внесённых объектов у пользователя
         * У пользователя могут быть:
         * - приборы учёта
         * - показания приборов учёта
         * - Что ещё?
         */
        $oQuery = new qbDelete('users', ['user_id', '=', $iUserId]);
        $oQuery->runMakeDelete();
        Core::getView()->addAlert('Пользователь удалён.', 2);
        Core::getView()->redirect(URL_HOME . 'users/');
    }



    protected function isRoleExist(){
        //TODO или проверять наличие роли в `roles` или проверять наличие роли в `roles_assig`
    }



    protected function addRole(int $iUserId, int $iRoleId)
    {
        $oQuery = new qbAddUpdate('roles_assig', [['user_id' => $iUserId], ['role_id' => $iRoleId]]);
        $oQuery->runMakeAdd();
        Core::getView()->addAlert('Роль добавлена.', 1);
    }

    protected function delRoleAssig(int $iAssigId)
    {
        $oQueryDel = new qbDelete('roles_assig', ['assig_id' => $iAssigId]);
        $oQueryDel->runMakeDelete();
        Core::getView()->addAlert('Роль удалена.', 2);
    }


    protected function addFilial(int $iUserId, int $iFilialId)
    {
        $oQuery = new qbAddUpdate('user_to_filial', [['user_id' => $iUserId], ['filial_id' => $iFilialId]]);
        $oQuery->runMakeAdd();
        Core::getView()->addAlert('Пользователь присоединён к филиалу.', 1);
    }

    protected function delFilialAssig(int $iAssigId)
    {
        $oQueryDel = new qbDelete('user_to_filial', ['assig_id' => $iAssigId]);
        $oQueryDel->runMakeDelete();
        Core::getView()->addAlert('Привязка пользователя к филиалу удалена.', 2);
    }


}