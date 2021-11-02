<?php


class meter_readings extends ModuleCommon implements iModules
{


    public function getModuleRoles(): array
    {
        return ['admin', 'mod_readings', 'operator'];
    }


    public function getTitle(): string
    {
        //return "Показания приборов учёта";
        return "Добавить показания";
    }


}