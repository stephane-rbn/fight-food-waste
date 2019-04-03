<?php

namespace App;

use Dotenv\Dotenv;

/**
 * Dotenv
 */
$dotEnv = Dotenv::create(dirname(__DIR__));
$dotEnv->load();

/**
 * Application configuration
 */
class Config
{
    /**
     * Set and get database host
     *
     * @return string
     */
    static function dbHost()
    {
        static $dbHost = null;

        if ($dbHost === null) {
            $dbHost = $_ENV['DB_HOST'];
        }

        return $dbHost;
    }

    /**
     * Set and get database name
     *
     * @return string
     */
    static function dbName()
    {
        static $dbName = null;

        if ($dbName === null) {
            $dbName = $_ENV['DB_NAME'];
        }

        return $dbName;
    }

    /**
     * Set and get database user
     *
     * @return string
     */
    static function dbUser()
    {
        static $dbUser = null;

        if ($dbUser === null) {
            $dbUser = $_ENV['DB_USER'];
        }

        return $dbUser;
    }

    /**
     * Set and get database password
     *
     * @return string
     */
    static function dbPwd()
    {
        static $dbPwd = null;

        if ($dbPwd === null) {
            $dbPwd = $_ENV['DB_PWD'];
        }

        return $dbPwd;
    }

    /**
     * Show or hide error messages on screen
     *
     * @return bool
     */
    static function showErrors()
    {
        static $showErrors = null;

        if ($showErrors === null) {
            $showErrors = filter_var($_ENV['SHOW_ERRORS'], FILTER_VALIDATE_BOOLEAN);
        }

        return $showErrors;
    }

    /**
     * Secret key for hashing
     *
     * @return string|null
     */
    static function secretKey()
    {
        static $secretKey = null;

        if ($secretKey === null) {
            $secretKey = $_ENV['SECRET_KEY_FOR_HASHING'];
        }

        return $secretKey;
    }
}
