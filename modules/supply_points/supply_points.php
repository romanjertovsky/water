<?php


class supply_points extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return['admin'];
    }

    public function getTitle(): string
    {
        return 'Точки поставки';
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }

}