<?php


class options extends ModuleCommon implements iModules
{


    public function getModuleRoles(): array
    {
        return ['*'];
    }

    public function getTitle(): string
    {
        return 'Настройки';
    }



}