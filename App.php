<?php

require_once 'Auth.php';

class App
{
    public static $db;
    public static $auth;

    public static function getDb(): \mysqli
    {
        if (!self::$db) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            self::$db = new mysqli('localhost', 'root', '', 'progress');
        }

        return self::$db;
    }

    public static function getAuth(): Auth
    {
        if (!self::$auth) {
            self::$auth = new Auth(self::getDb());
        }

        return self::$auth;
    }
}
