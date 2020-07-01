<?php
// 对象在计算机中是虚拟存在的东西，它的“重”和“轻”是指内存使用大小。
// flyweight模式：通过尽量共享实例来避免new出实例。

// Flyweight轻量级：按照通常方式编写程序会导致程序变重，所以如果能够共享实例会比较好，而Flyweight角色表示的就是那些实例会被共享的类。在示例程序中，由BigChar类扮演此角色
// FlyweightFactory轻量级工厂：生成Flyweight角色的工厂。在工厂中生成Flyweight角色可以实现共享实例。在示例程序中，由BigCharFactory类扮演此角色。
// Client请求者：Client角色使用Flyweight角色生成Flyweight角色。在示例程序中，由BigString类扮演此角色。

// 修改一个地方会对多个地方造成影响，这就是共享的特点。因此，在决定Flyweight角色中的字段时，需要精挑细选。只将那些真正应该在多个地方共享的字段定义在Flyweight角色中即可。
//
class BigChar
{
    private $_charname;
    private $_frontData;

    public function __construct($charname)
    {
        $this->_charname = $charname;
        $this->_frontData = "===={$charname}====";
    }

    public function printChar()
    {
        echo $this->_frontData . "\n";
    }
}

class BigCharFactory
{
    private $_pool = [];
    private static $_obj = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$_obj)) {
            self::$_obj = new self();
        }
        return self::$_obj;
    }

    public function getBigChar($charname): BigChar
    {
        if (!isset($this->_pool[$charname])) {
            $this->_pool[$charname] = new BigChar($charname);
        }
        return $this->_pool[$charname];
    }
}

class BigString
{
    private $_bigChars = [];

    public function __construct($str)
    {
        $factory = BigCharFactory::getInstance();
        for ($i = 0; $i < strlen($str); $i++) {
            $this->_bigChars[$i] = $factory->getBigChar($str[$i]);
        }
    }

    public function printStr()
    {
        foreach ($this->_bigChars as $bigChar) {
            $bigChar->printChar();
        }
    }
}

class flyweightMain
{
    public function run()
    {
        $bs = new BigString("lisi");
        $bs->printStr();
    }
}

(new flyweightMain())->run();