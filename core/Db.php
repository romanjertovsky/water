<?php


class Db
{


    private $PDO_link;


    public function __construct()
    { // Connect
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset="   . DB_CHAR;
        $pdo_options = [
            PDO::ATTR_ERRMODE =>                PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE =>     PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES =>       true   // TODO с этим параметром разобраться!
        ];
        $this->PDO_link = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
    }


    /** Выполняет запрос, и возвращает ссылку на результат
     * @param $sQuery
     * @return bool|PDOStatement
     */
    public function execute($sQuery)
    {
        $stmt = $this->PDO_link->prepare($sQuery);
        $stmt->execute();
        if(IS_DEBUG) Core::getView()->debug("Executed SQL: $sQuery");
        return $stmt;
    }


    /**
     * Выполняет SQL запрос и возвращает двумерный (строка / запись) ассоциативный массив.
     * Пользоваться методом без конструктора запросов (qb) не желательно,
     * проверки запроса нет.
     * @param string $sQuery - SQL запрос
     * @return array Массив вида [ [], [], [] ]
     */
    public function fetchArray(string $sQuery)
    {
        $stmt = $this->execute($sQuery);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result === false)
            return [];

        return $result;
    }


    /**
     * Возвращает одномерный ассоциативный массив по $sTableName и $aWhere
     * @param string $sTableName Имя таблицы 'table_name'
     * @param array $aWhere Условие WHERE ['a', '=', 1]
     * @return array
     */
    public function fetchRow(string $sTableName, array $aWhere)
    {

        // Если нет WHERE то метод не работает!
        if(empty($aWhere))
            die('Db->fetchRow() должен вернуть только одну строку, это не возможно без WHERE.');

        $oQuery = new qbSelect($sTableName, '*', $aWhere);
        $sQuery = $oQuery->makeSelect();
        $result = $this->fetchArray(
            $sQuery
        );

        // Если получившийся запрос возвращает более одной строки то метод не работает
        if(count($result) > 1)
            die("Db->fetchRow() должен вернуть только одну строку.
            [$sQuery] - данный запрос получил более одной строки из базы. Проверьте WHERE!");

        $result = $this->fetchArray(
            $oQuery->makeSelect()
        );

        if (isset($result[0])){
            return $result[0];
        } else {
            return [];
        }

    }


    /** сокр. от Quote. Заключает строку в кавычки экранирует специальные символы
     * для использования в запросе.
     * @param string $s об
     * @return false|string
     */
    public function q($s)
    {
        //if(!is_numeric($s)) TODO разобраться, возможно числа не стоит кавычить
        $s = $this->PDO_link->quote($s);
        return $s;
    }


}