<?php
// Observer观察者模式：当观察对象的状态发生变化时，会通知给观察者。Observer模式适用于根据对象状态进行相应处理的场景。

// Subject观察对象：定义了注册观察者和删除观察者的方法。此外，它还声明了“获取现在状态”的方法。在示例程序中，由NumberGenerator类扮演此角色。
// ConcreteSubject具体的观察对象：表示具体的被观察对象。当自身状态发生变化后，它会通知所有已经注册的Observer角色。在示例程序中，由RandomNumberGenerator类扮演此角色。
// Observer观察者：负责接收来自Subject角色状态变化的通知。为此，它声明了update方法。在示例程序中，由Observer接口扮演此角色。
// ConcreteObserver具体的观察者：当它的update方法被调用后，会去获取要观察的对象的最新状态。在示例程序中，由DigitObserver类和GraphObserver类扮演此角色。

// 可替换性：
//      利用抽象类和接口从具体类中抽出抽象方法。
//      在将实例作为参数传递至类中，或者在类的字段中保存实例时，不使用具体类型，而是使用抽象类型和接口。

// Observer的调用顺序应该不影响业务，保持各个ConcreteObserver类的独立性。

// Observer本来的意思是“观察者”，但实际上Observer角色并非主动地去观察，而是被动的接收来自Subject角色的通知。因此，Observer模式也被称为Publish-Subscribe（发布-订阅）模式。

interface Observer
{
    public function update(NumberGenerator $generator);
}

abstract class NumberGenerator
{
    public $_observers = [];

    public function addObserver(Observer $observer)
    {
        $this->_observers[] = $observer;
    }

    public function deleteObserver(Observer $observer)
    {
        foreach ($this->_observers as $key => $observer) {
            unset($this->_observers[$key]);
        }
    }

    public function notifyObservers()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    public abstract function getNumber(): int;

    public abstract function execute();
}

class RandomNumberGenerator extends NumberGenerator
{
    private $_number;

    public function getNumber(): int
    {
        return $this->_number;
    }

    public function execute()
    {
        for ($i = 0; $i < 20; $i++) {
            $this->_number = rand(0, 50);
            $this->notifyObservers();
        }
    }
}

class IncrementNumberGenerator extends NumberGenerator
{
    private int $_number;

    public function getNumber(): int
    {
        return $this->_number;
    }

    public function execute()
    {
        for ($i = 10; $i < 50; $i += 5) {
            $this->_number = $i;
            $this->notifyObservers();
        }
    }
}

class  DigitObserver implements Observer
{

    public function update(NumberGenerator $generator)
    {
        echo "DigitObserver:{$generator->getNumber()}\n";
        usleep(100000);
    }
}

class GraphObserver implements Observer
{
    public function update(NumberGenerator $generator)
    {
        echo "GraphObserver:\n";
        for ($i = 0; $i < $generator->getNumber(); $i++) {
            echo "*";
        }
        echo "\n";
        usleep(1000000);
    }
}

class ObserverMain
{
    public function run()
    {
        $generator = new RandomNumberGenerator();
        $observer1 = new DigitObserver();
        $observer2 = new GraphObserver();
        $generator->addObserver($observer1);
        $generator->addObserver($observer2);
//        $generator->execute();

        $generator2 = new IncrementNumberGenerator();
        $generator2->addObserver($observer1);
        $generator2->addObserver($observer2);
        $generator2->execute();
    }
}

(new ObserverMain())->run();