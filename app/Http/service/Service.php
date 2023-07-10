<?php
namespace App\Http\service;

abstract class Service
{
    public function __construct() {}

    private static $_instance = [];

    public static function instance($className = __CLASS__) {
        if (isset(self::$_instance[$className])) {
            return self::$_instance[$className];
        } else {
            $instance = self::$_instance[$className] = new $className(null);
            return $instance;
        }
    }


}