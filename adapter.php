<?php
// **************************************************
// Target对象：负责定义所需的方法;本例子中 interface BPrint
// Client（请求者）：负责使用Target角色所定义的方法进行具体处理；本例子中 MainAdapterExtend和MainAdapterDelegate
// Adaptee （被适配）：持有特定方法的角色；本例子中 Banner
// Adapter（适配）：使用Adaptee角色的方法来满足Target角色的需求，这是Adapter模式的目的，也是Adapter角色的作用。
//                  本例子中的 DelegatePrintBanner和ExtendPrintBanner
//
// 在类适配器模式中，Adapter角色通过继承来使用Adaptee角色，而在对象适配器模式中，Adapter角色通过委托来使用Adaptee角色
// **************************************************


// Adaptee （被适配）：持有特定方法的角色
class Banner
{
    private $_string;

    public function __construct($str)
    {
        $this->_string = $str;
    }

    // 括号展示
    public function showWithParen()
    {
        var_dump("({$this->_string})");
        return;
    }

    // 星号展示
    public function showWithAster()
    {
        var_dump("*{$this->_string}*");
        return;
    }
}

// Target对象：负责定义所需的方法
interface BPrint
{
    public function printWeak();

    public function printStrong();
}

// ---------------------------------使用继承实现适配器模式：类适配器模式-------------------------------------------
// Adapter（适配）：使用Adaptee角色的方法来满足Target角色的需求，这是Adapter模式的目的，也是Adapter角色的作用
class ExtendPrintBanner extends Banner implements BPrint
{

    public function __construct($str)
    {
        parent::__construct($str);
        return (BPrint::class);
    }

    public function printWeak()
    {
        $this->showWithParen();
    }

    public function printStrong()
    {
        $this->showWithAster();
    }
}

// MainAdapterExtend并不知道PrintBanner怎么实现，
// 可以在不修改MainAdapterExtend的情况下修改PrintBanner的实现
// Client（请求者）：负责使用Target角色所定义的方法进行具体处理
class MainAdapterExtend
{
    public function run()
    {
        $p = new ExtendPrintBanner("Hello extend"); // BPrint
        $p->printWeak();
        $p->printStrong();
    }
}

(new MainAdapterExtend())->run();

// ---------------------------------使用委托实现适配器模式：对象适配器模式---------------------------------------------------
// Adapter（适配）：使用Adaptee角色的方法来满足Target角色的需求，这是Adapter模式的目的，也是Adapter角色的作用
class DelegatePrintBanner implements BPrint
{
    private $_banner;

    public function __construct(Banner $banner)
    {
        $this->_banner = $banner;
    }

    public function printWeak()
    {
        return $this->_banner->showWithParen();
    }

    public function printStrong()
    {
        return $this->_banner->showWithAster();
    }
}

// Client（请求者）：负责使用Target角色所定义的方法进行具体处理
class MainAdapterDelegate
{
    public function run()
    {
        $p = new DelegatePrintBanner(new Banner("Hello Delegate")); // BPrint
        $p->printWeak();
        $p->printStrong();
    }
}

(new MainAdapterDelegate())->run();