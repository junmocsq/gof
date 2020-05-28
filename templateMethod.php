<?php
// Template Method模式，处理的流程在父类中，而具体的实现则交给子类。

// 在父类中定义处理流程的框架，在子类中实现具体处理的模式就是template method模式
// AbstractClass(抽象类)：不仅负责实现模板方法，还负责声明模板中所使用到的抽象方法。
//      这些抽象方法由子类ConcreteClass角色负责实现。此例子中AbstractDisplay扮演此角色

// ConcreteClass(具体类)：负责实现AbstractClass角色中定义的抽象方法。
//      这里实现的方法将会被AbstractClass角色的模板方法调用。本例子中，CharDisplay和StringDisplay扮演此角色。

// 在Template Method模式中，父类和子类是紧密联系、共同工作的。
//      因此，在子类中实现父类中声明的抽象方法时，必须要理解这些抽象方法被调用的时机。
//      在看不到父类的源代码的情况下，想要编写出子类是非常困难的。

// 无论在父类类型的变量保存在那个子类的实例，程序都可以正常工作，这种原则称为里氏替换原则（The Liskov Substitution Principle，LSP）。
//      当然，LSP并非仅限于Template Method模式，它是通用的继承原则。

/*
 * 父类对子类的要求
 *  在子类的立场
 *      在子类中可以使用父类定义的方法
 *      可以通过在子类中增加方法以实现新的功能
 *      在子类中重写父类的方法可以改变程序的行为
 *  父类的角度
 *      期待子类取实现抽象方法
 *      要求子类去实现抽象方法
 *  也就是说，子类具有实现在父类中所声明的抽象方法的责任。因此，这种责任被称为“子类责任”。
 */

// 抽象类的意义：在抽象类阶段确定处理的流程非常重要。

// 父类与子类的相互协作支撑起了整个程序。虽然将更多的方法的实现放在父类中会让子类变得更轻松，
//      但是同时也降低了子类的灵活性；反之，如果父类中实现的方法过少，子类就会变得臃肿不堪，而且还会导致各子类间的代码出现重复。

// PHP 5 支持抽象类和抽象方法。定义为抽象的类不能被实例化。
// 任何一个类，如果它里面至少有一个方法是被声明为抽象的，那么这个类就必须被声明为抽象的。
// 被定义为抽象的方法只是声明了其调用方式（参数），不能定义其具体的功能实现。
abstract class AbstractDisplay
{
    abstract public function open();

    abstract public function print();

    abstract public function close();

    // 调用抽象方法的此方法为模板方法
    // 如果父类中的方法被声明为 final，则子类无法覆盖该方法。如果一个类被声明为 final，则不能被继承。
    final public function display()
    {
        $this->open();
        for ($i = 0; $i < 5; $i++) {
            $this->print();
        }
        $this->close();
    }
}

class CharDisplay extends AbstractDisplay
{
    private $_ch;

    public function __construct($ch)
    {
        $this->_ch = $ch;
    }

    public function open()
    {
        echo "<<";
    }

    public function print()
    {
        echo $this->_ch;
    }

    public function close()
    {
        echo ">>\n";
    }


}

class StringDisplay extends AbstractDisplay
{
    private $_str = "";
    private $_width = 0;

    public function __construct($str)
    {
        $this->_str = $str;
        $this->_width = strlen($str);
    }

    public function open()
    {
        $this->_printLine();
    }

    public function print()
    {
        echo "|{$this->_str}|\n";
    }

    public function close()
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

// 测试程序行为
class TemplateMethodMain
{
    public function run()
    {
        $d1 = new CharDisplay("H");
        var_dump($d1 instanceof AbstractDisplay); // bool(true)
        $d2 = new StringDisplay("Hello, World!");
        $d3 = new StringDisplay("你好！");
        $d1->display();
        $d2->display();
        $d3->display();
    }
}

(new TemplateMethodMain())->run();;