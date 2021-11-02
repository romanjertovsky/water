<?php


class roles extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return['admin'];
    }


    public function getTitle(): string
    {
        return 'Роли';
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }


    protected function getUsersWithRole(int $iRoleId)
    {// TODO костыль

        // запрос - где эта роль уже назначена
        $oQuery = new qbSelect('view_user_to_roles', '*', ['role_id' => $iRoleId]);
        $aAssignedRoles = $oQuery->runMakeSelect();

        // Заполняем массив именами пользователей, которым установлена данная роль
        $aUsersWithThisRole = [];
        foreach ($aAssignedRoles as $key => $val) {
            $aUsersWithThisRole[] = $val['user_login'];
        }

        return $aUsersWithThisRole;

    }


}