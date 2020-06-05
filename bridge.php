<?php
// 将类的功能层次结构与实现层次结构分离
// Bridge模式的作用是在“类的功能层次结构”和“类的实现层次结构”之间搭建桥梁。

// 类的功能层次结构:父类具有基本的功能；在子类中增加新的功能。
// 类的实现层次结构：父类通过声明抽象方法来定义接口；子类通过实现具体方法来实现接口。

// 继承是强关联关系，但委托是弱关联关系。

// Abstraction抽象化：该角色位于“类的功能层次”的最上层。它使用Implementor角色的实例。在示例程序中，由Display类扮演此角色
// RefinedAbstraction改善后的抽象化：在Abstraction角色的基础上增加了新功能的角色。在示例程序中，由CountDisplay类扮演此角色
// Implementor实现者：该角色位于“类的实现层次结构”的最上层。它定义了用于实现Abstraction角色的接口的方法。在示例程序中，由DisplayImpl类扮演此角色。
// ConcreteImplementor具体实现者：该角色负责实现在Implementor角色中定义的接口。在示例程序中，由StringDisplayImpl类扮演此角色

class Display
{
    private $_impl;

    public function __construct(DisplayImpl $impl)
    {
        $this->_impl = $impl;
    }

    public function open()
    {
        $this->_impl->rawOpen();
    }

    public function print()
    {
        $this->_impl->rawPrint();
    }

    public function close()
    {
        $this->_impl->rawClose();
    }

    public final function display()
    {
        $this->open();
        $this->print();
        $this->close();
    }
}

class CountDisplay extends Display
{
    public function __construct(DisplayImpl $impl)
    {
        parent::__construct($impl);
    }

    public function multiDisplay(int $times)
    {
        $this->open();
        for ($i = 0; $i < $times; $i++) {
            $this->print();
        }
        $this->close();
    }
}

class RandomDisplay extends Display
{
    public function __construct(DisplayImpl $impl)
    {
        parent::__construct($impl);
    }

    public function randomDisplay(int $times)
    {
        $this->open();
        $max = rand(0, $times);
        for ($i = 0; $i < $max; $i++) {
            $this->print();
        }
        $this->close();
    }
}

abstract class DisplayImpl
{
    abstract public function rawOpen();

    abstract public function rawPrint();

    abstract public function rawClose();
}

// 类的实现层次结构
class StringDisplayImpl extends DisplayImpl
{
    private $_string;
    private $_width;

    public function __construct(string $str)
    {
        $this->_string = $str;
        $this->_width = mb_strlen($str);
    }

    public function rawOpen()
    {
        $this->_printLine();
    }

    public function rawPrint()
    {
        echo "|{$this->_string}|\n";
    }

    public function rawClose()
    {
        $this->_printLine();
    }

    private function _printLine()
    {
        echo "+";
        for ($i = 0; $i < $this->_width; $i++) {
            echo "-";
        }
        echo "+\n";
    }
}

assert_options(ASSERT_BAIL);

class FileDisplayImpl extends DisplayImpl
{
    private $_reader;
    private $_filename;

    public function __construct(string $filename)
    {
        $this->_filename = $filename;
        assert(file_exists($this->_filename));
    }

    public function rawOpen()
    {
        $this->_reader = fopen($this->_filename, "r");
        $this->_printLine();
    }

    public function rawPrint()
    {
        while (!feof($this->_reader)) {
            $text = fgets($this->_reader);
            if ($text) echo $text;
        }
        echo "\n";
    }

    public function rawClose()
    {
        $this->_printLine();
        fclose($this->_reader);
    }

    private function _printLine()
    {
        echo "+-------------------------------+\n";
    }
}

class BridgeMain
{
    public function run()
    {
        $d1 = new Display(new StringDisplayImpl("Hello ,Japan!"));
        $d2 = new Display(new StringDisplayImpl("Hello ,World!"));
        $d3 = new CountDisplay(new StringDisplayImpl("Hello ,USA!"));
        $d4 = new RandomDisplay(new StringDisplayImpl("Hello ,UK!"));

        $d1->display();
        $d2->display();
        $d3->display();
        $d3->multiDisplay(5);
        $d4->randomDisplay(5);

        $d5 = new Display(new FileDisplayImpl("singleton.php"));
        $d5->display();
    }
}

(new BridgeMain())->run();