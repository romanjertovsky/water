<?php


class User
{


    private $aUserArray = [
        'user_id'       =>  -1,     // у всех юзеров в базе данных id > 0
        'user_login'    =>  '',
        'password_md5'  =>  '',
        'common_name'   =>  '',
        'email'         =>  ''
    ];

    private $aUserRoles = [];


    public function __construct()
    {

        if(isset($_SESSION['user_login'])) {

            $aTempUserArray = $this->loadUserFromDb($_SESSION['user_login']);

            if ($aTempUserArray['user_login'] == $_SESSION['user_login']) {

                $this->aUserArray = $aTempUserArray;
                $this->aUserRoles = $this->loadRolesFromDb($_SESSION['user_login']);

                session_regenerate_id();
            }
        }

    }


    private function loadUserFromDb($sUsername): array
    {
        return Core::getDb()->fetchRow(
            'users',
            ['user_login' => $sUsername]);
    }


    /**
     * Возвращает массив с ролями (role_name) любого пользователя
     * @param int|string $isUser id или имя пользователя
     * @return array ['список', 'ролей', 'пользователя']
     *
     * Если $isUser число, то находит по user_id, если строка то по user_login
     *
     */
    public function loadRolesFromDb($isUser): array
    {

        $sFieldName = 'role_name';

        if(is_numeric($isUser)) {
        // Если пользователь запрошен по id
            $aWhere = ['user_id' => $isUser];
        } else {
            $aWhere = ['user_login' => $isUser];
        }

        $oQuery = new qbSelect('view_user_to_roles', $sFieldName, $aWhere);

        $aResult = $oQuery->runMakeSelect();
        $aRoles = [];
        foreach ($aResult as $val) {
            $aRoles[] = $val[$sFieldName];
        }
        return $aRoles;
    }


    public function getUserArray() {
        return $this->aUserArray;
    }


    public function getUserId() {
        return $this->aUserArray['user_id'];
    }


    public function getUserRoles() {
        return $this->aUserRoles;
    }


    public function isBlocked() {
        if(in_array('blocked', $this->getUserRoles())) {
            return true;
        } else {
            return false;
        }
    }


    public function isLoggedIn() {
        if($this->aUserArray['user_id'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $this->aUserArray['user_id'] = -1;
        unset($_SESSION['user_login']);
    }


}