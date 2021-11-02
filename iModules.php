<?php


interface iModules
{


    public function getModuleRoles(): array;
    /*
     * Возвращает массив с ролями пользователей,
     * которые имеют доступ
     */


    public function getTitle(): string;
    /*
     * возвращает <title>Заголовок страницы</title>
     */


    public function getContent(): string;
    /*
     * Возвращает
        ob_start();
        результат работы модуля
        return ob_get_clean();
       или любую строку, которая будет помещена в основной шаблон
     */


    public function moduleInit($aModuleParams);
    /*
     * Основная обработка
     */


}