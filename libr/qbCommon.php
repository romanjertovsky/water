<?php


class qbCommon
{


    /* VARIABLES */
    protected $sTable = '';         // 'users'
    protected $aWhere = [];         // WHERE ['field', '=', 'val'] или [['field', '=', 'val'], ['field', '=', 'val']]


    /* SETTERS */


    public function setSTable(string $sTable)
    {
        $this->sTable = $sTable;
    }
    public function setAWhere($asWhere)
    {
        $this->aWhere = $this->isNotArray($asWhere);
    }


    /* METHODS */


    /** Собирает выражение из массивов для запроса. В значениях экранирует строки и спецсимволы.
     * Подразумевается, что в БД нет полей с числовым именем.
     * Примеры принимаемых массивов:
     * $variant_1 = ['xxx' => 1];
     * $variant_2 = ['xxx', '>',  5];
     * $variant_3 = [['xxx' => 5], ['yyy' => 10]];
     * $variant_4 = [['xxx', '<',  5], ['yyy', '>',  10]];
     * Результат для примеров выше:
     * `xxx` = 1
     * `xxx` > 5
     * `xxx` = 5 AND `yyy` = 10
     * `xxx` < 5 AND `yyy` > 10
     * @param array $aCondition принимаемый массив
     * @param string $sOperand операнд, если условий несколько и массив двумерный
     * @return string возвращает строку.
     */
    protected function makeCondition(array $aCondition, $sOperand = 'AND'): string
    {

        $aResult = [];

        $siFirstKey = array_key_first($aCondition);

        if(is_array($aCondition[$siFirstKey]))
        {

        }

        if(is_numeric($siFirstKey)) {

        }


        foreach ($aCondition as $key => $val) {

            if(is_array($val)) {

                // вот здесь
                $aResult[] = $this->parseArray($val);

            } else {

                $aResult[] = $this->parseArray($aCondition);
                break;

            }

        }

        return implode(" $sOperand ", $aResult);

    }


    /** Вспомогательный метод для makeCondition().
     */
    private function parseArray($aArray) {

        switch (count($aArray)) {

            case 1: // в массиве один элемент, это вариант 1 или 3
                $key = array_key_first($aArray);
                $sResult = "`$key` = " . Core::getDb()->q($aArray[$key]);
                break;

            case 3: // в массиве три элемента, это вариант с операндом, 2 или 4

                $sResult = "`{$aArray[0]}` {$aArray[1]} " . Core::getDb()->q($aArray[2]);
                break;

            default:
                die('qbCommon::makeCondition() - непредвиденное кол-во элементов в аргументе $aCondition.');

        }

        return $sResult;

    }


    /** Проверяет, является ли значение массивом. Если нет, то помещает его в одномерный массив.
     * Данный метод применяется для той пользы, что все условия обрабатываются foreach
     * @param $val
     * @return array
     */
    protected function isNotArray($val)
    {
        if(is_array($val)) {
            return $val;
        } else {
            return [$val];
        }
    }


}