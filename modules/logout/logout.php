<?php


class logout extends ModuleCommon implements iModules
{


    public function getModuleRoles(): array
    {
        return ['*'];
    }


    public function moduleInit($aModuleParams)
    {
        Core::getUser()->logout();
        Core::getView()->redirect(URL_HOME);
    }


}