<?php

namespace Core;

use \PDO;
use App\Config;

/**
 * Base Model
 *
 * PHP version 7.2
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
}
