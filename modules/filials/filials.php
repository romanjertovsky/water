<?php


class filials extends ModuleCommon implements iModules
{
    public function getModuleRoles(): array
    {
        return['admin'];
    }


    public function getTitle(): string
    {
        return 'Филиалы';
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }


}