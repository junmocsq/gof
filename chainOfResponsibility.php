<?php
// 职责链模式（chain of responsibility）：将多个对象组成一条职责链，然后按照它们在职责链上的顺序一个一个地找出到底应该谁来负责处理。

// Handler处理者：定义了处理请求的接口。Handler角色知道“下一个处理者”是谁，如果自己无法处理请求，它会将请求转给“下一个处理者”。当然，“下一个处理者”也是Handler角色。在示例中，Support类扮演此角色。负责处理请求的是support方法。
// ConcreteHandler具体的处理者。在示例程序中，由NoSupport、LimitSupport、OddSupport、SpecialSupport等各个类扮演此角色。
// Client请求者：Client角色是向第一个ConcreteHandler角色发送请求的角色，在示例中，由ChainMain类扮演此角色。

// chain of responsibility 模式优点和缺点
// 最大的优点：它弱化了发出请求的人（Client角色）和处理请求的人（ConcreteHandler角色）之间的关系。
// 可以动态的改变职责链
// ConcreteHandler可以专注于自己的工作
// 职责链模式相比于直接调用，会产生处理延迟。

class Trouble
{
    private int $_number;

    public function __construct(int $number)
    {
        $this->_number = $number;
    }

    public function getNumber()
    {
        return $this->_number;
    }

    public function __toString()
    {
        return "Trouble [{$this->_number}]";
    }
}

abstract class Support
{
    private string $_name;
    private $_next = null;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function setNext(Support $next): Support
    {
        $this->_next = $next;
        return $this->_next;
    }

    public final function support(Trouble $trouble)
    {
        // 递归
        if ($this->resolve($trouble)) {
            $this->done($trouble);
        } elseif (!is_null($this->_next)) {
            $this->_next->support($trouble);
        } else {
            $this->fail($trouble);
        }

        // 循环
//        for ($obj = $this; true; $obj = $obj->_next) {
//            if ($obj->resolve($trouble)) {
//                $obj->done($trouble);
//                break;
//            } elseif (is_null($obj->_next)) {
//                $obj->fail($trouble);
//                break;
//            }
//        }
    }

    protected abstract function resolve(Trouble $trouble): bool;

    protected function done(Trouble $trouble)
    {
        echo "{$trouble} is resolved by {$this} .\n";
    }

    protected function fail(Trouble $trouble)
    {
        echo "{$trouble} connot be resolved .\n";
    }

    public function __toString()
    {
        return "[{$this->_name}]";
    }
}

// 永远不解决问题的类
class NoSupport extends Support
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

    protected function resolve(Trouble $trouble): bool
    {
        return false;
    }
}


class LimitSupport extends Support
{
    private int $_limit;

    public function __construct($name, int $limit)
    {
        $this->_limit = $limit;
        parent::__construct($name);
    }

    protected function resolve(Trouble $trouble): bool
    {
        if ($trouble->getNumber() < $this->_limit) {
            return true;
        } else {
            return false;
        }
    }
}

class OddSupport extends Support
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

    protected function resolve(Trouble $trouble): bool
    {
        if ($trouble->getNumber() % 2 == 1) {
            return true;
        } else {
            return false;
        }
    }
}

class SpecialSupport extends Support
{
    private int $_number;

    public function __construct($name, int $number)
    {
        $this->_number = $number;
        parent::__construct($name);
    }

    protected function resolve(Trouble $trouble): bool
    {
        if ($trouble->getNumber() == $this->_number) {
            return true;
        } else {
            return false;
        }
    }
}


class ChainMain
{
    public function run()
    {
        $alice = new NoSupport("Alice");
        $bob = new LimitSupport("Bob", 100);
        $charlie = new SpecialSupport("Charlie", 429);
        $diana = new LimitSupport("Diana", 200);
        $elmo = new OddSupport("Elmo");
        $fred = new LimitSupport("Fred", 300);
        $alice->setNext($bob)->setNext($charlie)->setNext($diana)->setNext($elmo)->setNext($fred);
        for ($i = 0; $i < 500; $i += 33) {
            $alice->support(new Trouble($i));
        }
    }
}

(new ChainMain())->run();