<?php


class test extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return['admin'];
    }

    public function getTitle(): string
    {
        return 'ТЕСТОВЫЙ МОДУЛЬ';
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }


}