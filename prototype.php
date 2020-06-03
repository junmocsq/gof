<?php
// 通过复制生成实例


// 原型Prototype
// 定义用于复制现有实例来生成新实例的方法
abstract class ProductProto
{
    abstract public function use(string $s);

    // 此处用到Template Method模式
    final public function createClone(){
        // 深拷贝
        return clone $this;
    }

    private $aaa = 1;

    public function incrA($a)
    {
        $this->aaa += $a;
    }

    public function printA()
    {
        var_dump("aaa:{$this->aaa}");
    }
}

// Client 使用者
// 负责使用复制实例的方法生成新的实例
class Manager
{
    private $_showCase;

    public function register(string $name, ProductProto $proto)
    {
        $this->_showCase[$name] = $proto;
    }

    private function _getProto(string $protoname): ProductProto
    {
        $p = $this->_showCase[$protoname]??null;
        if(is_null($p)){
            throw new Exception("{$protoname} is not exists!");
        }
        return $p;
    }

    public function create(string $protoname): ProductProto
    {
        $p = $this->_getProto($protoname);

        return $p->createClone();
    }

}

// ConcretePrototype 具体的类型
// 负责实现复制现有实例并生成新实例的方法。
class MessageBox extends ProductProto
{
    private $_decochar;

    public function __construct($decochar)
    {
        $this->_decochar = $decochar;
    }


    public function use(string $s)
    {
        $length = strlen($s);
        echo str_repeat($this->_decochar, $length + 4);
        echo "\n";
        echo "{$this->_decochar} {$s} {$this->_decochar}\n";
        echo str_repeat($this->_decochar, $length + 4);
        echo "\n";
    }
}

class UnderlinePen extends ProductProto
{
    private $_ulchar;

    public function __construct($ulchar)
    {
        $this->_ulchar = $ulchar;
    }

    public function use(string $s)
    {
        $length = strlen($s);
        echo "\" {$s} \"\n";
        echo str_repeat($this->_ulchar, $length + 4);
        echo "\n";
    }
}


class PrototypeMain
{

    public function run()
    {
        $manager = new Manager();
        $upen = new UnderlinePen("~");
        $mbox = new MessageBox("*");
        $sbox = new MessageBox("/");
        $manager->register("strong message", $upen);
        $manager->register("warning box", $mbox);
        $manager->register("slash box", $sbox);

        $p1 = $manager->create("strong message");
        $p1->use("Hello, World.");
        $p1->incrA(10);
        $p1->printA(); // string(6) "aaa:11"
        $p4 = $manager->create("strong message");
        $p4->use("Hello, World.");
        $p4->printA(); // string(5) "aaa:1"


        $p2 = $manager->create("warning box");
        $p2->use("Hello, World.");
        $p3 = $manager->create("slash box");
        $p3->use("Hello, World.");
    }
}

(new PrototypeMain())->run();