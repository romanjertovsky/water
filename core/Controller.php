<?php


class Controller
{

    private $sModName = '';
    private $aModParams = array();
    private $oModule;


    public function __construct()
    {

        if (Core::getUser()->isLoggedIn()) {        // залогинен

            // Заблокирован
            if (Core::getUser()->isBlocked()) {
                Core::getUser()->logout();
                Core::getView()->addAlert("Ваш пользователь заблокирован.", 3);
                Core::getView()->redirect(URL_HOME);
            }

            $aURL = $this->aRouter();

            // если урл пустой или в нём есть параметры
            if (empty($aURL)) {
                $this->sModName = 'default_module';
            } else {
                $this->sModName = array_shift($aURL);
                $this->aModParams = $aURL;
            }

            // проверка прав доступа
            if(
                in_array('*', $this->getOModule($this->sModName)->getModuleRoles())   // есть * в ролях модуля
                ||
                !empty($this->getMatchedRoles($this->sModName))         // не пусто в массиве совпадающих прав
            ) {
                // права доступа есть, ничего не делаем
            } else {
                // прав доступа нету, редирект на HOME
                Core::getView()->addAlert("Отсутствуют права доступа к модулю $this->sModName. Загружен модуль по умолчанию.", 3);
                Core::getView()->redirect(URL_HOME);
            }

        } else {
            // пользователь не залогинен
            $this->sModName = 'login';
            $this->aModParams = [];
        }

    }


    public function appStart()
    {

        if(IS_DEBUG) if(IS_DEBUG) Core::getView()->debug("
        Controller()->appStart(): вызван модуль {$this->sModName}
        с параметрами: [" . implode($this->aModParams, ', ') . ']');


        // вызов логики модуля
        $this
            ->getOModule($this->sModName)
            ->moduleInit($this->aModParams);


        // Передаём результаты работы модуля во View
        Core::getView()->setSTitle(
            $this->getOModule($this->sModName)->getTitle()
        );

        Core::getView()->setSModContent(
            $this->getOModule($this->sModName)->getContent()
        );


        // А после того как модуль отработал и вернул результат работы
        print Core::getView()->getRenderLayout();

    }


    /**
     * @param $sModName
     * @return iModules
     */
    private function getOModule($sModName)
    {// возвращает объект выбранного модуля, объект всегда синглтон
        if(empty($this->oModule)) {
            $sModulePath = DIR_MODS . $sModName . DS . $sModName . '.php';
            if (!file_exists($sModulePath)) {
                // если файл модуля не существует
                Core::getView()->addAlert("Модуля $sModName не существует. Загружен модуль по умолчанию.", 3);
                Core::getView()->redirect(URL_HOME);
            }
            require_once DIR_MODS . 'ModuleCommon.php';
            require_once $sModulePath;
            $this->oModule = new $sModName;
        }
        return $this->oModule;
    }


    public function getMatchedRoles($sModuleName) {
        // получаем совпадающие роли Модуля и текущего пользователя
        return array_intersect(
            $this->getOModule($sModuleName)->getModuleRoles(),
            Core::getUser()->getUserRoles());
    }


    private function aRouter()
    {
        $aParam = array();
        if (array_key_exists('url', $_GET)) {
            // в .htaccess реализована проверка на символы ([a-z0-9_\/]+)
            $aParam = explode('/', $_GET['url']);
        }
        return $aParam;
    }


    public function getSModName()
    {
        return $this->sModName;
    }


}