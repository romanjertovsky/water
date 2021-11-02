<?php


class default_module extends ModuleCommon implements iModules
{


    public function getModuleRoles(): array
    {
        return ['*'];
    }

    public function getTitle(): string
    {
        return 'Главная';
    }

    public function getContent(): string
    {
        return "
        <h5>Добро пожаловать в систему учёта электроэнергии.</h5>
        <br><br>
        Желаем успехов!
        ";
    }


}