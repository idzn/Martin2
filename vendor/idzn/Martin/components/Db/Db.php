<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Db;

use \PDO;

class Db
{
    private $config;
    /**
     * @var PDO $pdo
     */
    public $pdo;
    /**
     * @var \PDOStatement
     */
    private $statement;

    private $sqlTemplates = [
        'select' => '{{select_section}} {{from_section}} {{where_section}} {{order_section}} {{limit_section}}',
    ];

    private $resultSQL;
    private $select_section;
    private $from_section;
    private $where_section;
    private $order_section;
    private $limit_section;
    private $binds = [];
    private $placeholderNumber = 0;


    public function __construct($config)
    {
        $this->config = $config;
        $this->pdo = new PDO($this->config['dsn'],
            $this->config['user'],
            $this->config['pass'],
            [
                PDO::ATTR_PERSISTENT => ($this->config['persistent']) ? true : false,
            ]);
        if ($config['errMode'] == 'silent')
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
        elseif ($config['errMode'] == 'warning')
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        elseif ($config['errMode'] == 'exception')
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        /*
        if ($this->pdo->getAttribute (PDO::ATTR_DRIVER_NAME) == 'mysql')
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        */
        //$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * @param $sql
     * @return Db
     */
    public function query($sql, $binds = [])
    {
        try {
            $this->statement = $this->pdo->prepare($sql);
            $this->statement->execute($binds);
            $this->init();
            return $this;
        } catch(\PDOException $e){
            echo 'DB error : '.$e->getMessage();
            exit;
        }
    }

    public function beginTransaction(){
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
        return $this;
    }

    public function insert($tableName, $data = [])
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ',array_map(function($value) { return $this->pdo->quote($value); }, array_values($data)));
        return $this->query("INSERT INTO $tableName ($columns) VALUES ($values)");
    }

    public function lastInsertID($name = null)
    {
        return $this->pdo->lastInsertID($name);
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    public function resultArray()
    {
        $data = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        return (count($data) == 1) ? $data[0] : $data;
    }

    public function where($param, $value)
    {
        $operator = ' = ';
        preg_match_all('|([^>=<!]+)([>=<!]+)|', $param, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches[0])) {
            $param = trim($matches[1][0]);
            $operator = ' ' . $matches[2][0] . ' ';
        }
        $paramPlaceholder = ':param_' . $this->getPlaceholderNumber();
        $this->where_section = 'WHERE ' . $param . $operator . $paramPlaceholder . ' ';

        $this->binds[$paramPlaceholder] = $value;
        return $this;
    }

    private function _where($param, $value, $pretext)
    {
        $operator = ' = ';
        preg_match_all('|([^>=<!]+)([>=<!]+)|', $param, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches[0])) {
            $param = trim($matches[1][0]);
            $operator = ' ' . $matches[2][0] . ' ';
        }
        $placeholder = $this->getMakedPlaceholder($value);
        $this->where_section = $this->where_section . " $pretext " . $param . $operator . $placeholder . ' ';

        return $this;
    }

    public function andWhere($param, $value)
    {
        return $this->_where($param, $value, 'AND');
    }

    public function orWhere($param, $value)
    {
        return $this->_where($param, $value, 'OR');
    }

    private function _whereIn($param, $array, $pretext)
    {
        $operator = " $pretext ";

        $placeholders = [];
        foreach ($array as $one)
        {
            $placeholders[] = $this->getMakedPlaceholder($one);
        }
        $placeholders = '(' . implode(',', $placeholders) . ')';

        $this->where_section = 'WHERE ' . $param . $operator . $placeholders . ' ';

        return $this;
    }

    public function whereIn($param, $array)
    {
        return $this->_whereIn($param, $array, 'IN');
    }

    public function whereNotIn($param, $array)
    {
        return $this->_whereIn($param, $array, 'NOT IN');
    }

    public function limit($offset = null, $limit = null)
    {
        $this->limit_section = 'LIMIT ';
        if ($offset !== null) {
            $this->limit_section .= intval($offset) . ', ';
        }
        if ($limit !== null) {
            $this->limit_section .= intval($limit) . ' ';
        }
        return $this;
    }

    private function getMakedPlaceholder($var)
    {
        $placeholderNumber = $this->getPlaceholderNumber();
        $placeholder = ':param_' . $placeholderNumber;
        $this->binds[$placeholder] = $var;
        return $placeholder;
    }

    public function get($table)
    {
        $this->select_section = 'SELECT * ';
        $this->from_section = 'FROM ' . $this->config['tablePrefix'] . addslashes($table) . ' ';
        return $this->prepareSQL('select')
            ->query($this->resultSQL, $this->binds)
            ->resultArray();
    }

    public function count($table)
    {
        $this->select_section = 'SELECT count(*) ';
        $this->from_section = 'FROM ' . $this->config['tablePrefix'] . addslashes($table) . ' ';
        return $this->prepareSQL('select')
            ->query($this->resultSQL, $this->binds)
            ->resultArray()['count(*)'];
    }

    private function prepareSQL($queryType)
    {
        $this->resultSQL = $this->sqlTemplates[$queryType];
        $this->resultSQL = str_replace('{{select_section}}', $this->select_section, $this->resultSQL);
        $this->resultSQL = str_replace('{{from_section}}',   $this->from_section, $this->resultSQL);
        $this->resultSQL = str_replace('{{where_section}}',  $this->where_section, $this->resultSQL);
        $this->resultSQL = str_replace('{{order_section}}',  $this->order_section, $this->resultSQL);
        $this->resultSQL = str_replace('{{limit_section}}',  $this->limit_section, $this->resultSQL);

        return $this;
    }

    private function getPlaceholderNumber()
    {
        return ++ $this->placeholderNumber;
    }

    private function init()
    {
        $this->placeholderNumber = 0;
        $this->resultSQL = null;
        $this->select_section = null;
        $this->from_section = null;
        $this->where_section = null;
        $this->order_section = null;
        $this->limit_section = null;
    }

    public function getLastSqlString()
    {
        return $this->statement->queryString;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}