<?php

namespace Core;

use \PDO;
use App\Config;

/**
 * Base Model
 */
abstract class Model
{
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $connection = null;

        if ($connection === null) {
            $dsn = 'mysql:host=' . Config::dbHost() . ';dbname=' . Config::dbName() . ';charset=utf8';

            $connection = new PDO($dsn, Config::dbUser(), Config::dbPwd());

            // Throw an Exception when a database error occurs
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $connection;
    }

    // /**
    //  *
    //  *
    //  * @param PDOStatement $statement
    //  *
    //  * @return array
    //  */
    // private static function sqlSelectFetchAll(PDOStatement $statement){
    //     return $statement->fetchAll(PDO::FETCH_ASSOC);
    // }

    /**
     * Prepare et execute SQL queries
     *
     * @param string $sql
     * @param array $params
     *
     * @return mixed
     */
    private static function query($sql, $params = [])
    {
        if ($statement = self::getDB()->prepare($sql)) {
            $paramId = 1;

            if (count($params)) {

                foreach ($params as $param) {

                    if (is_int($param)) {
                        $statement->bindValue($paramId, $param, PDO::PARAM_INT);
                    } else {
                        $statement->bindValue($paramId, $param, PDO::PARAM_STR);
                    }

                    $paramId++;
                }
            }
        }

        return $statement;
    }

//    Commented examples
//    $users = parent::find('donors', [
//        'conditions' => '',
//        'bind' => [],
//        'order' => 'first_name',
//        'limit' => 5,
//    ]);
    // /**
    //  *
    //  *
    //  * @param string $table
    //  * @param array $params
    //  *
    //  * @return array|bool
    //  */
    // private static function read($table, $params = [])
    // {
    //     $conditionString = '';
    //     $bind = [];
    //     $order = '';
    //     $limit = '';

    //     // Build query's conditions part based on $params['conditions'] value
    //     if (isset($params['conditions'])) {

    //         if (is_array($params['conditions'])) {
    //             foreach ($params['conditions'] as $condition) {
    //                 $conditionString .= ' ' . $condition . ' AND';
    //             }

    //             $conditionString = rtrim(trim($conditionString), ' AND');
    //         } else {
    //             $conditionString = $params['conditions'];
    //         }

    //         if ($conditionString !== '') {
    //             $conditionString = ' WHERE ' . $conditionString;
    //         }

    //     }

    //     // Bind
    //     if (array_key_exists('bind', $params)) {
    //         $bind = $params['bind'];
    //     }

    //     // Order
    //     if (array_key_exists('order', $params)) {
    //         $order = ' ORDER BY `' . $params['order'] . '`';
    //     }

    //     // Limit
    //     if (array_key_exists('limit', $params)) {
    //         $limit = ' LIMIT ' . $params['limit'];
    //     }

    //     $sql = "SELECT * FROM `{$table}`{$conditionString}{$order}{$limit}";

    //     $statement = self::query($sql, $bind);

    //     if ($statement->execute()) {
    //         if (count(self::sqlSelectFetchAll($statement))) {
    //             return self::sqlSelectFetchAll($statement);
    //         }
    //     }

    //     return false;
    // }

    // /**
    //  *
    //  *
    //  * @param string $table
    //  * @param array $params
    //  *
    //  * @return array|bool
    //  */
    // protected static function find($table, $params = [])
    // {
    //     return is_array(self::query($table, $params)) ? self::query($table, $params) : false;
    // }

    // protected static function findFirst($table, $params = [])
    // {
    //     return is_array(self::query($table, $params)) ? self::query($table, $params)[0] : false;
    // }

    /**
     * Insert data in specified table and fields in database
     *
     * @param string $table The table data are going to be inserted into
     * @param array $fields Associative array that contains values and matching fields
     *
     * @return bool
     */
    protected static function insert($table, $fields = [])
    {
        $fieldString = '';
        $valueString = '';
        $values = [];

        foreach ($fields as $field => $value) {
            $fieldString .= '`' . $field . '`,';
            $valueString .= '?,';
            $values[] = $value;
        }

        $table = '`' . $table . '`';
        $fieldString = rtrim($fieldString, ',');
        $valueString = rtrim($valueString, ',');

        $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";

        return self::query($sql, $values)->execute();
    }

    // /**
    //  * Update data in specified table and fields in database
    //  *
    //  * @param string $table The table data are going to be updated in
    //  * @param int $id Data element id
    //  * @param array $fields Associative array that contains values and matching fields
    //  *
    //  * @return bool
    //  */
    // protected static function update($table, $id, $fields = [])
    // {
    //     $fieldString = '';
    //     $values = [];

    //     foreach ($fields as $field => $value) {
    //         $fieldString .= ' `' . $field . '` = ?,';
    //         $values[] = $value;
    //     }

    //     $fieldString = rtrim(trim($fieldString), ',');

    //     $sql = "UPDATE {$table} SET {$fieldString} WHERE `id` = {$id}";

    //     return self::query($sql, $values)->execute;
    // }

    // /**
    //  * Delete data from specified table
    //  *
    //  * @param string $table The table data are going to be deleted from
    //  * @param int $id Data element id
    //  *
    //  * @return bool
    //  */
    // protected static function delete($table, $id)
    // {
    //     $sql = "DELETE FROM `{$table}` WHERE `id` = {$id}";

    //     return self::query($sql)->execute;
    // }
}
