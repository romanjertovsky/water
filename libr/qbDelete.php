<?php


class qbDelete extends qbCommon
{


    public function __construct(string $sTable = '', $aWhere = [])
    {
        $this->sTable = $sTable;
        $this->aWhere = $aWhere;
    }


    public function makeDelete(): string
    {
        $sQuery = "DELETE FROM `{$this->sTable}` WHERE "
            . $this->makeCondition($this->aWhere);

        if(IS_DEBUG) Core::getView()->debug("qbCommon::makeDelete: $sQuery");

        return $sQuery;
    }


    public function runMakeDelete()
    {
        Core::getDb()->execute($this->makeDelete());
    }


}