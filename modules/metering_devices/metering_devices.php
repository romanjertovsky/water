<?php


class metering_devices extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return ['admin'];
    }

    public function getTitle(): string
    {
        return "Приборы учёта";
    }


    public function moduleInit($aModuleParams)
    {
        $this->setParams($aModuleParams);
    }

}