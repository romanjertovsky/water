<?php


class qbDirectory extends qbSelect
{


    /* VARIABLES */

    private $sSubTableNotIn = '';
    private $aSubWhereNotIn = [];


    /* SETTERS */

    public function setSSubTableNotIn(string $sSubTable): void
    {
        $this->sSubTableNotIn = $sSubTable;
    }
    public function setASubWhereNotIn($asSubWhere): void
    {
        $this->aSubWhereNotIn = $this->isNotArray($asSubWhere);
    }


    /* METHODS */

    /**
     * qbDirectory constructor.
     *
     * Пример запроса:     <br>
     * SELECT `role_id` as `0`, `role_name` as `1` <br>
     * FROM `roles` <br>
     * WHERE `role_id` NOT IN <br>
     * (SELECT `role_id` FROM `roles_assig` WHERE `user_id` = " . Core::getDb()->q($iUserId) . ") <br>
     * ORDER BY `role_name` <br>
     * @param string $sTable - имя таблицы-справочника
     * @param array $aFields - минимум два поля - id и данные ['id', 'username'], все остальные поля будут присоеденены за вторым через ";"
     * @param array $aWhere
     * @param string $sSubTableNotIn - таблица для подзапроса NOT IN чтобы исключить уже используемые записи
     * @param array $asSubWhereNotIn -
     *
     *
     */
    public function __construct(string $sTable = '', array $aFields = [], array $aWhere = [], $sSubTableNotIn = '', $asSubWhereNotIn = [])
    {
        $this->setSTable($sTable);
        $this->setAFields($aFields); // ДВА id и данные
        $this->setAWhere($aWhere);

        $this->setSSubTableNotIn($sSubTableNotIn);
        $this->setASubWhereNotIn($asSubWhereNotIn);

        $this->setAAs([0, 1]); // Чтобы класс генерил обычный массив с ключами 0 и 1
    }



    public function makeDirectory()
    {

        // SELECT `поля`, `через`, `запятую` FROM
        $sQuery2 =
            "SELECT " .
            '`' .  implode('`, `', $this->aFields) . '`' .
            " FROM `{$this->sTable}`";

        // Нахера то что выше, когда в родительском классе есть makeSelect()? Пока не понятно!
        $sQuery = $this->makeSelect();

        // добавляем NOT IN
        // Если WHERE уже есть в основном запросе то NOT IN добавляем через AND, иначе через WHERE

        if(!empty($this->sSubTableNotIn)) {


            if(!empty($this->aWhere))
                $sQuery .= ' AND';
            else
                $sQuery .= ' WHERE';

            $sSubQuery = "SELECT `{$this->aFields[0]}` FROM `{$this->sSubTableNotIn}`";

            // доп. WHERE для SUB QUERY
            if(!empty($this->aSubWhereNotIn))
                $sSubQuery .= " WHERE " . $this->makeCondition($this->aSubWhereNotIn);

            $sQuery .= " `{$this->aFields[0]}` NOT IN ($sSubQuery)";

        }


        return $sQuery . ';';


    }


    public function runMakeDirectory()
    {

        $sQuery = $this->makeDirectory();

        $aData = Core::getDb()->fetchArray(
            $sQuery
        );

        $aResult = [];
        foreach ($aData as $key => $val) {

            $aResult[$key][0] = array_shift($aData[$key]);
            $aResult[$key][1] = implode('; ', $aData[$key]);

        }

        return $aResult;

    }


}