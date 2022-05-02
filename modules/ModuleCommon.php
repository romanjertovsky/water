<?php


class ModuleCommon implements iModules
{

    /*
     * Родительский класс любого модуля. Помимо интерфейса.
     * При наследовании данного класса классом модуля закрывает
     * все обязательные методы дефолтными пустыми значениями
     * Так, что классом можно сразу пользоваться.
     *
     * Данный класс имеет ключевые, для всех модулей, методы:
     * setParams($aModuleParams) - принимает пользовательский url, и разбирает его на add/edit а так-же
     * определяет номер страницы
     *
     * getContent() и , которые выполняют основные действия:
     * html.php
     * html_add.php
     * html_edit.php
     *
     *
     * */


    private $aModuleParams = [''];
    protected $iPageNo = 1;


    public function getModuleRoles(): array
    {
        return[];
    }


    public function getTitle(): string
    {
        return '';
    }


    public function moduleInit($aModuleParams)
    {
        // Если модуль использует какие-то параметры из URL (как страницы) то
        // перегрузить этот метод и включать строку ниже!

        // $this->setParams($aModuleParams);
    }


    // Возвращает соотв. html метода, при необходимости метод перегрузить.
    public function getContent(): string
    {

        $sModulePath = DIR_MODS . Core::getController()->getSModName() . DS;

        ob_start();

        switch ($this->aModuleParams[0]) {

            case 'add':
                $sModulePath .= 'html_add.php';
                break;

            case 'edit':
                $sModulePath .= 'html_edit.php';
                break;

            default:
                $sModulePath .= 'html.php';
                break;

        }

        if(file_exists($sModulePath)) {
            include $sModulePath;
        } else {
            Core::getView()->addAlert("Модуль не имеет html-формы для данного действия!", 3);
        }

        return ob_get_clean();

    }


    /* * * * * * Ниже библиотека общих методов. То, что не надо перегружать * * * * * */

    protected function checkEmpty($var, $sMsg)
    {
        $iErr = 0;
        if(empty($var)) {
            $iErr = 1;
            Core::getView()->addAlert($sMsg, 2);
        }
        return $iErr;
    }

    protected function checkIsNumeric($var, $sMsg)
    { // $var должно быть числовым

        $iErr = 0;
        if(!is_numeric($var)) {
            $iErr = 1;
            Core::getView()->addAlert($sMsg, 2);
        }
        return $iErr;
    }

    protected function checkNoNumeric($var, $sMsg)
    { // $var НЕ должно быть числовым

        $iErr = 0;
        if(is_numeric($var)) {
            $iErr = 1;
            Core::getView()->addAlert($sMsg, 2);
        }
        return $iErr;
    }

    protected function checkExist($sTable, $aWhere, $sMsg)
    {
        $iErr = 0;
        if(!empty(
            Core::getDb()->fetchRow(
                $sTable,
                $aWhere
            )
        )) {
            $iErr = 1;
            Core::getView()->addAlert($sMsg, 2);
        }
        return $iErr;
    }


    /** Соединяет множество элементов в два - id и значение, для справочников
     * $this->id2val('supply_points', ['point_id' => 1], ['point_name', 'dispatch_name'], ', ');
     * @param string $sTable
     * @param array $aWhere
     * @param array $aFields - поле(я) которые будут собраны в значение
     * @param string $sGlue - "соединитель"
     * @return string
     */
    protected function id2val(string $sTable, array $aWhere, array $aFields, string $sGlue = ''): string
    {
        $aResult = [];
        $aData = Core::getDb()->fetchRow($sTable, $aWhere);

        foreach ($aFields as $val) {

            $aResult[] = $aData[$val];

        }

        return implode($sGlue, $aResult);

    }

    /** То же что и id2val, только для массива
     * @param array $aData массив данных
     * @param string $sIdField имя поля-идентификатора в $aData и в справочнике
     * @param string $sTable таблица-справочник
     * @param array $aFields поле(я), которые берём из справочника
     * @param string $sGlue соединитель полей, если их несколько
     * @return array
     */
    protected function a_id2val(
        array $aData,
        string $sIdField,
        string $sTable,
        array $aFields,
        string $sGlue = ''
    ): array
    {

        foreach ($aData as $key => $val) {

            $iTempId = $aData[$key][$sIdField];

            $aData[$key][$sIdField] =
                $this->id2val(
                    $sTable,
                    [$sIdField => $iTempId],
                    $aFields,
                    $sGlue = $sGlue
                );

        }

        return $aData;

    }


    /**
     * @param string $sTable - таблица в к-рой справочник используется
     * @param array $aFields - поля, которые пойдут в вывод
     * @param string $sIdName - имя поля в который ставится id связи
     * @param int $iUsedId - идентификатор, который (ссылается) используется в таблицах связи
     * @return array
     */
    protected function getRecordsUsed(string $sTable, array $aFields, string $sIdName, int $iUsedId)
    { // TODO метод доработать и вместо "костылей" подставить

        $oQuery = new qbSelect($sTable, $aFields, [$sIdName => $iUsedId]);
        $aAssignedRows = $oQuery->runMakeSelect();

        $aResultRows = [];

        foreach ($aAssignedRows as $key => $val) {
            $aResultRows[] = implode($val, ', ');
        }

        return $aResultRows;

    }


    /**
     * Если форма достаточно обширна с большим количеством полей, то имеет смысл
     * вынести её на отдельную страницу.
     * Предусмотрены два типа форм-страниц add и edit.
     * add - добавить запись в БД, edit - редактировать/удалить существующую,
     *
     * Если $aModuleParams[0] - число, то это номер страницы для формы html (не забыть перегрузить moduleInit())
     * Если $aModuleParams[0] = /add/ или /edit/ то вызываем соотв. форму
     *
     * В случае вызова /edit/{int} дополнительно происходит проверка, на то, что следующий параметр
     * числовой, и его можно использовать для запроса в БД.
     *
     * @param array $aModuleParams массив с параметрами, переданный из Контроллера.
     */
    protected function setParams($aModuleParams)
    {

        if(isset($aModuleParams[0])){

            if (is_numeric($aModuleParams[0])
                && $aModuleParams[0] >= 1
            ) {
                // устанавливаем № страницы
                $this->iPageNo = $aModuleParams[0];

            } elseif (
                $aModuleParams[0] == 'add'
            ) {
                $this->aModuleParams[0] = 'add';
            } elseif (
                $aModuleParams[0] == 'edit'
                &&
                isset($aModuleParams[1])
                &&
                is_numeric($aModuleParams[1])
            ) {
                $this->aModuleParams[0] = 'edit';
                $this->aModuleParams[1] = $aModuleParams[1];
            }

        }

    }


}