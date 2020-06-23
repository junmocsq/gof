<?php
// Memento模式：保存对象状态，实现以下功能：撤销（Undo）、重做（Redo）、History（历史记录）和Snapshot（快照）

// Originator生成者：保存自己最新状态时生成Memento角色。当把以前保存的Memento角色传递给Originator角色时，它会自己恢复至生成该Memento角色时的状态。在示例中，由Gamer类扮演此角色
// Memento纪念品：Memento角色会将Originator角色的内部信息整合在一起。在Memento角色中虽然保存了Originator角色的信息，但它不会向外公布这些信息。
// Caretaker负责人：当Caretaker角色想要保存当前的Originator角色的状态时，会通知Originator角色。Originator角色在接收到通知后会生成Memento角色的实例并将其返回给Caretaker角色。由于以后可能会用Memento实例来将Originator恢复至原来的状态，因此Caretaker角色会一直保存Memento实例，在示例中，由MementoMain类扮演。

// 划分Caretaker和Originator是为了职责分担，
//      变更为可以多次撤销
//      变更为不仅可以撤销，还可以将现在的状态保存在文件中。
class Memento
{
    public $money;
    public $fruits = [];

    public function getMoney()
    {
        return $this->money;
    }

    public function __construct($money)
    {
        $this->money = $money;
    }

    public function addFruit($fruit)
    {
        $this->fruits[] = $fruit;
    }

    public function getFruits()
    {
        return $this->fruits;
    }
}

class Gamer
{
    private $_money;
    private $_fruits = [];
    private static $fruitsName = ["苹果", "葡萄", "香蕉", "橘子"];

    public function __construct($money)
    {
        $this->_money = $money;
    }

    public function getMoney()
    {
        return $this->_money;
    }

    public function bet()
    {
        $r = rand(1, 6);
        if ($r == 1) {
            $this->_money += 100;
            echo "所持金钱增加了。\n";
        } elseif ($r == 2) {
            $this->_money /= 2;
            echo "所持金钱减半了\n";
        } elseif ($r == 6) {
            $f = $this->getFruit();
            echo "获得了水果（{$f}）\n";
            $this->_fruits[] = $f;
        } else {
            echo "什么都没有发生\n";
        }
    }

    public function createMemento(): Memento
    {
        $memento = new Memento($this->_money);
        foreach ($this->_fruits as $fruit) {
            if (mb_substr($fruit, 0, 3) == "好吃的") {
                $memento->addFruit($fruit);
            }
        }
        return $memento;
    }

    public function restoreMemento(Memento $memento)
    {
        $this->_money = $memento->getMoney();
        $this->_fruits = $memento->getFruits();

    }

    public function __toString()
    {
        return "[money = {$this->_money}, fruits = " . implode(" ", $this->_fruits) . "]";
    }

    public function getFruit()
    {
        $prefix = "";
        if (rand(0, 1)) {
            $prefix = "好吃的";
        }
        return $prefix . self::$fruitsName[rand(0, count(self::$fruitsName) - 1)];
    }
}


class MementoMain
{
    public function run()
    {
        $gamer = new Gamer(100);
        $memento = $gamer->createMemento();
        for ($i = 0; $i < 100; $i++) {
            echo "=================={$i}\n";
            echo "当前状态:{$gamer}\n";
            $gamer->bet();
            echo "所持有金钱{$gamer->getMoney()}元。\n";
            if ($gamer->getMoney() > $memento->getMoney()) {
                echo "   （所持有金钱增加了许多，因此保存游戏当前状态）\n";
                $memento = $gamer->createMemento();
            } elseif ($gamer->getMoney() < $memento->getMoney() / 2) {
                echo "  （所持金钱减少了许多，因此将游戏恢复至以前的状态）\n";
                $gamer->restoreMemento($memento);
            }
            usleep(1000000);
            echo "\n";
        }
    }
}


(new MementoMain())->run();