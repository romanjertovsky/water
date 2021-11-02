<?php


class qbSelect extends qbCommon
{


    /* VARIABLES */
    protected $aFields = [];        // ['user_id', 'user_login']
    protected $aAs = [];            // SELECT ['user_id', 'user_login'] AS ['id', 'login'] - вот оно!
    protected $aOrderBy = [];       // ORDER BY
    protected $bDesc = false;       // ORDER BY ... DESC
    protected $aLimit = [];         // LIMIT [1, 2]
    protected $iCount = -1;         // Кол-во записей по текущему запросу


    /* SETTERS */


    public function setAFields($asFields)
    {
        $this->aFields = $this->isNotArray($asFields);
    }
    public function setAAs($asAs)
    {
        $this->aAs = $this->isNotArray($asAs);
    }
    public function setAOrderBy($asOrderBy, $bDesc = false)
    {
        $this->aOrderBy =$this->isNotArray($asOrderBy);
        $this->bDesc = $bDesc;
    }
    public function setALimit(int $iA, int $iB = 0)
    {
        $this->aLimit[0] = $iA;
        $this->aLimit[1] = $iB;
    }


    /* METHODS */

    /**
     * qbSelect constructor.
     * @param string $sTable имя таблицы
     * @param array|string $asFields массив имён полей. Одно поле можно строкой.
     * @param array $asWhere
     * @param array $asOrderBy ['user_id']
     */
    public function __construct($sTable = '', $asFields = [], $asWhere = [], $asOrderBy = [])
    {
        $this->setSTable($sTable);
        $this->setAFields($asFields);
        $this->setAWhere($asWhere);
        $this->setAOrderBy($asOrderBy);
    }


    public function makeSelect(): string
    {

        // SELECT FIELDS
        $aFields = [];
        if(empty($this->aFields['0']) || $this->aFields[0] == '*') {
            // Если список полей пуст или там *
            $aFields = ['*'];
        } else {
            foreach ($this->aFields as $key => $val) {
                $aFields[] = "`$val`" .
                    (isset($this->aAs[$key])
                        ?
                        " AS `{$this->aAs[$key]}`"
                        :
                        '');
            }
        }
        $sQuery = 'SELECT ' . implode(', ', $aFields);

        // FROM
        $sQuery .= " FROM `{$this->sTable}`";

        // WHERE
        if(!empty($this->aWhere)) {
            $sQuery .= ' WHERE ' . $this->makeCondition($this->aWhere);
        }

        // ORDER BY
        if(!empty($this->aOrderBy)) {
            $sQuery .= " ORDER BY `" . implode('`, `', $this->aOrderBy) . "`" . ($this->bDesc ? ' DESC' : '');
        }

        // LIMIT
        if(!empty($this->aLimit)) {
            $sQuery .= " LIMIT {$this->aLimit[0]}" . ($this->aLimit[1]>0 ? ", {$this->aLimit[1]}" : '');
        }


        if(IS_DEBUG) Core::getView()->debug("qbCommon::makeSelect: $sQuery");
        return $sQuery;

    }


    public function runMakeSelect()
    {
        return Core::getDb()->fetchArray(
            $this->makeSelect()
        );
    }


    public function makeSelectPaginated(int $iPage) // = 1
    {
        if(empty($this->aOrderBy))
            die('Установите $aOrderBy перед вызовом makeSelectPaginated()!!!');
        $iOffset = ($iPage - 1) * PAGE_SIZE;            // смещение, с какой записи в базе начинаем
        $iCount = $this->getCount();
        $iTotalPages = ceil($iCount / PAGE_SIZE);

        $this->setALimit($iOffset, PAGE_SIZE);
        return $this->makeSelect();
    }


    public function runMakeSelectPaginated(int $iPage)
    {
        return Core::getDb()->fetchArray(
            $this->makeSelectPaginated($iPage)
        );
    }


    /**
     * Возвращает число кол-ва записей в таблице, по текущему запросу!
     * @return int кол-во записей в таблице
     */
    public function getCount(): int
    {

        if($this->iCount == -1) {

            $sQuery = "SELECT count(*) AS counted FROM `{$this->sTable}`" .
                (empty($this->aWhere)
                    ?
                    ''
                    :
                    ' WHERE ' .
                    $this->makeCondition($this->aWhere)
                );

            $this->iCount = Core::getDb()->fetchArray($sQuery)['0']['counted'];

        }

        return $this->iCount;

    }


}