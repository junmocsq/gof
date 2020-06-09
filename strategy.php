<?php
// 整体的替换算法
// 使用strategy模式可以整体地替换算法的实现部分。能够整体地替换算法，能让我们轻松地以不同的算法去解决同一个问题。

// Strategy策略：strategy角色负责决定实现策略所必需的接口；在示例程序中，由Strategy接口扮演
// ConcreteStrategy具体的策略：负责实现Strategy角色的接口，即负责实现具体的策略（战略、方向`方法和算法）。在示例程序中，由WinningStrategy类和ProbStrategy类扮演此角色。
// Context上下文：负责使用Strategy。Content角色保存ConcreteStrategy角色的实例，并使用ConcreteStrategy角色去实现需求（总之，还是要调用Strategy角色的接口）。在示例程序中，由Player类扮演此角色。
// Strategy模式特意将算法与其他部分分离开来，只是定义了算法相关的接口，然后在程序中以委托的方式来使用算法。使用委托这种弱关联关系可以很方便地整体替换算法。

class Hand
{
    const HANDVALUE_GUU = 0;    // 石头的值
    const HANDVALUE_CHO = 1;    // 剪刀的值
    const HANDVALUE_PAA = 2;    // 布的值

    private $_nameArr = [
        "石头", "剪刀", "布"
    ];

    public static $handArr = [

    ];

    private $_handvalue;

    private function __construct($handvelue)
    {
        $this->_handvalue = $handvelue;
    }

    public static function getHand(int $handvalue)
    {
        if (!isset(self::$handArr[$handvalue])) {
            self::$handArr[$handvalue] = new Hand($handvalue);
        }
        return self::$handArr[$handvalue];
    }

    // this是否强于h
    public function isStrongerThan(Hand $h)
    {
        return $this->_fight($h) == 1;
    }

    // this是否弱于h
    public function isWeakerThan(Hand $h)
    {
        return $this->_fight($h) == -1;
    }

    // 0 平 1 this胜 -1 this输
    private function _fight(Hand $h): int
    {
        if ($this->_handvalue == $h->_handvalue) {
            return 0;
        } else if (($this->_handvalue + 1) % 3 == $h->_handvalue) {
            return 1;
        } else {
            return -1;
        }
    }

    public function __toString(): string
    {
        return $this->_nameArr[$this->_handvalue];
    }
}

interface Strategy
{
    // 获取下一局出的手势
    public function nextHand(): Hand;

    // 上一局是否获胜了
    public function study(bool $win);

}

class WinningStrategy implements Strategy
{
    /*
     * 上一局赢了，下一局出相同的，上一局输了，下一局随机出
     */

    private $_won = false;
    private $_prevHand = null;

    public function __construct()
    {
    }

    public function nextHand(): Hand
    {
        if (!$this->_won) {
            $this->_prevHand = Hand::getHand(rand(0, 2));
        }
        return $this->_prevHand;
    }

    public function study(bool $win)
    {
        $this->_won = $win;
    }
}

class ProbStrategy implements Strategy
{
    private $_prevHandleValue = 0;
    private $_currentHandValue = 0;
    private $_history = [
        [1, 1, 1],
        [1, 1, 1],
        [1, 1, 1],
    ];

    public function nextHand(): Hand
    {
        $bet = rand(0, $this->_getSum($this->_currentHandValue));
        $handvalue = 0;
        if ($bet < $this->_history[$this->_currentHandValue][0]) {
            $handvalue = 0;
        } else if ($bet < $this->_history[$this->_currentHandValue][0] + $this->_history[$this->_currentHandValue][1]) {
            $handvalue = 1;
        } else {
            $handvalue = 2;
        }
        $this->_prevHandleValue = $this->_currentHandValue;
        $this->_currentHandValue = $handvalue;
        return Hand::getHand($handvalue);
    }

    private function _getSum(int $hv)
    {
        $sum = 0;
        for ($i = 0; $i < 3; $i++) {
            $sum += $this->_history[$hv][$i];
        }
        return $sum;
    }

    public function study(bool $win)
    {
        if ($win) {
            $this->_history[$this->_prevHandleValue][$this->_currentHandValue]++;
        } else {
            $this->_history[$this->_prevHandleValue][($this->_currentHandValue + 1) % 3]++;
            $this->_history[$this->_prevHandleValue][($this->_currentHandValue + 2) % 3]++;
        }
    }
}

class Player
{
    private $_name;
    private $_strategy;
    private $_wincount;
    private $_lostcount;
    private $_gamecount;

    public function __construct(string $name, Strategy $strategy)
    {
        $this->_name = $name;
        $this->_strategy = $strategy;
    }

    public function nextHand()
    {
        return $this->_strategy->nextHand();
    }

    public function win()
    {
        $this->_strategy->study(true);
        $this->_wincount++;
        $this->_gamecount++;
    }

    public function lose()
    {
        $this->_strategy->study(false);
        $this->_lostcount++;
        $this->_gamecount++;
    }

    public function even()
    {
        $this->_gamecount++;
    }

    public function __toString()
    {
        return "[{$this->_name}:{$this->_gamecount} games,{$this->_wincount} win,{$this->_lostcount} lose]";
    }
}

class StrategyMain
{
    public function run()
    {
        $player1 = new Player("Taro", new WinningStrategy());
        $player2 = new Player("Hana", new ProbStrategy());
        for ($i = 0; $i < 10000; $i++) {
            $nextHand1 = $player1->nextHand();
            $nextHand2 = $player2->nextHand();

            if ($nextHand1->isStrongerThan($nextHand2)) {
                echo "Winner:" . $player1 . "\n";
                $player1->win();;
                $player2->lose();;
            } elseif ($nextHand1->isWeakerThan($nextHand2)) {
                echo "Winner:" . $player2 . "\n";
                $player1->lose();;
                $player2->win();;
            } else {
                echo "even...\n";
                $player1->even();
                $player2->even();
            }
        }
        echo "Total Result:\n";
        echo "{$player1}\n";
        echo "{$player2}\n";
    }
}

(new StrategyMain())->run();;