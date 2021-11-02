<?php


class cities extends ModuleCommon implements iModules
{

    public function getModuleRoles(): array
    {
        return['admin'];
    }


    public function getTitle(): string
    {
        return 'Населённые пункты';
    }


    public function moduleInit($aModuleParams)
    {
        // Если модуль использует какие-то параметры из URL (как страницы) то
        // перегрузить этот метод и включать строку ниже!

        $this->setParams($aModuleParams);
    }

}