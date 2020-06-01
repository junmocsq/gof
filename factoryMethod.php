<?php
// 将实例的生成交给子类

// 用Template Method模式来构建生成实例的工厂，这就是Factory Method模式。

// 在Factory Method模式中，父类决定实例的生成方式，但并不决定所要生成的具体的类，具体的处理全部交给子类负责。
//      这样就可以将生成实例的框架(framework)和实际负责生成实例的类解耦

// Product产品:定义在Factory Method模式中生成的那些实例所持有的接口(API)。本例中由Product扮演

// Creator创建者:负责生成Product角色的抽象类，具体的处理有子类ConcreteCreator角色决定。本例中由Factory扮演。
//      Creator角色对于实际负责生成实例的ConcreteCreator角色一无所知，他唯一知道的就是，
//      只要调用Product角色和生成实例的方法，就可以生成Product实例。在示例中，createProduct方法就是生成实例的方法。
//      不用new关键字来生成实例，而是调用生成实例的专用方法来生成实例，这样就可以防止父类与其他具体类耦合。

// ConcreteProduct具体的产品:属于具体加工的一方，它决定了具体的产品。在示例中的IDCard

// ConcreteCreator具体的创建者:数据具体加工的这一方，它负责生成具体的产品。在示例程序中IDCardFactory

abstract class Product
{
    abstract public function use();
}


abstract class Factory
{
    final public function create(string $owner)
    {
        $p = $this->createProduct($owner);
        $this->registerProduct($p);
        return $p;
    }

    abstract protected function createProduct(string $owner): Product;

    abstract protected function registerProduct(Product $product);
}

class IDCard extends Product
{
    private $_owner;
    private $_serial;

    public function __construct($owner, $serial)
    {
        echo "制作{$owner}的ID卡\n";
        $this->_owner = $owner;
        $this->_serial = $serial;
    }

    public function use()
    {
        echo "使用{$this->_owner}的ID卡,卡号为:{$this->_serial}\n";
    }


    public function serial()
    {
        return $this->_serial;
    }

    public function getOwner()
    {
        return $this->_owner;
    }
}

class IDCardFactory extends Factory
{
    private $_owners = [];
    private $_serial = 100;

    protected function createProduct(string $owner): Product
    {
        return new IDCard($owner, $this->_serial++);
    }

    protected function registerProduct(Product $product)
    {
        $this->_owners[] = $product->getOwner();
    }

    public function getOwners()
    {
        return $this->_owners;
    }
}

class FactoryMethodMain
{
    public function run()
    {
        $factory = new IDCardFactory();
        $card1 = $factory->create("小明");
        $card2 = $factory->create("小红");
        $card3 = $factory->create("小刚");
        $card1->use();
        $card2->use();
        $card3->use();

        print_r($factory->getOwners());
    }
}

(new FactoryMethodMain())->run();