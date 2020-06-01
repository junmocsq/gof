<?php
// 当我们想在程序中表示某个东西只会存在一个，就会有“只能创建一个实例”的需求
// 确保只生成一个实例的模式被称为Singleton模式。Singleton是指含有一个元素的集合。

class Singleton
{
    private static $_obj = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$_obj)) {
            self::$_obj = new Singleton();
        }
        return self::$_obj;
    }

    public function read()
    {
        var_dump(__CLASS__ . ":read");
    }
}


class SingletonMain
{
    public function run()
    {
        Singleton::getInstance()->read(); // Singleton:read

        // new Singleton(); // Call to private Singleton::__construct() from invalid context
        $obj1 = Singleton::getInstance();
        $obj2 = Singleton::getInstance();
        var_dump($obj1 === $obj2); // bool(true)
    }
}

(new SingletonMain())->run();