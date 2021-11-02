<?php


class qbAddUpdate extends qbCommon
{


    private $aData = [];        // ['field_name' => $value]


    public function setAData(array $aData)
    {
        $this->aData = $aData;
    }


    /**
     * qbAddUpdate constructor
     * @param string $sTable имя таблицы
     * @param array $aData ['field_name' => $value]
     * @param array $aWhere Используется только в UPDATE ['field', '=', 'val']
     */
    public function __construct(string $sTable = '', array $aData = [], $aWhere = [])
    {
        $this->sTable   = $sTable;
        $this->aData = $aData;
        $this->aWhere = $aWhere;
    }


    public function makeAdd()
    {
        $sQuery = "INSERT INTO `{$this->sTable}` SET {$this->makeCondition($this->aData, ',')}";

        if(IS_DEBUG) Core::getView()->debug("qbAddUpdate::makeAdd: $sQuery");

        return $sQuery;
    }


    public function runMakeAdd()
    {
        Core::getDb()->execute($this->makeAdd());
    }


    public function makeUpdate()
    {

        $sQuery = "UPDATE `{$this->sTable}` SET "
            . $this->makeCondition($this->aData, ',')
            . " WHERE "
            . $this->makeCondition($this->aWhere);

        if(IS_DEBUG) Core::getView()->debug("qbAddUpdate::makeUpdate: $sQuery");

        return $sQuery;
    }


    public function runMakeUpdate()
    {
        Core::getDb()->execute($this->makeUpdate());
    }


}