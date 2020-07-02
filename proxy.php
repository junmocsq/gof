<?php
// 只在必要时生成实例。
// Proxy模式：Proxy是代理人的意思，它指的是代替别人进行工作的人。当不一定需要本人亲自进行工作时，就可以寻找代理人去完成工作。但是代理人毕竟只是代理人，能代理本人完成的工作有限。因此，当代理人遇到无法自己解决的事情时就会去找本人帮忙。
// 在面向对象中，“本人”和“代理人”都是对象。如果“本人”太忙了，有些工作无法自己完成，就将其交给“代理人”对象负责。

// Subject主体：Subject定义了使Proxy角色和RealSubject角色之间具有一致性的接口。由于存在Subject角色，所以Client角色不必在意它使用的究竟是Proxy角色还是RealSubject角色。在示例程序中，由Printable接口扮演。
// Proxy代理人：该角色会尽量处理来自Client角色的请求。只有当自己不能处理时，它才会将工作交给RealSubject角色。Proxy角色只有在必要的时候才会生成RealSubject角色。Proxy角色实现了在Subject中定义的接口(API)。在示例程序中，由PrinterProxy类扮演此角色。
// RealSubject实际的主体：“本人”RealSubject会在“代理人”Proxy角色无法胜任工作时出场。它与Proxy角色一样，也实现了在Subject角色中定义的接口。在示例程序中，由Printer类扮演此角色。
// Client请求者：使用Proxy模式的角色。由ProxyMain类扮演。

// 使用代理人来提升处理速度

interface Printable
{
    public function setPrinterName($name);

    public function getPrinterName();

    public function printer(string $str);
}

class Printer implements Printable
{
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
        $this->heavyJob("Printer的实例生成中");
    }

    public function setPrinterName($name)
    {
        $this->_name = $name;
    }

    public function getPrinterName()
    {
        return $this->_name;
    }

    public function printer(string $str)
    {
        echo "======{$this->_name}======\n";
        echo "$str\n";
    }

    public function heavyJob(string $str)
    {
        echo "$str";
        for ($i = 0; $i < 5; $i++) {
            sleep(1);
            echo ".";
        }
        echo "结束。\n";
    }

}

// 不论setPrinterName方法和getPrinterName方法被调用多少次，都不会生成Printer类的实例。只有当真正需要本人时，才会生成Printer类的实例。（PrinterProxy类的调用者完全不知道是否生成了本人。也不用在意是否生成了本人）。
// Printer类并不知道PrinterProxy类的存在。即Printer类并不知道自己到底是通过PrinterProxy被调用的还是直接被调用的。
class PrinterProxy implements Printable
{
    private $_name;
    private $_real = null;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function setPrinterName($name)
    {
        $this->_name = $name;
    }

    public function getPrinterName()
    {
        return $this->_name;
    }

    public function printer(string $str)
    {
        $this->realize();
        $this->_real->printer($str);
    }

    public function realize()
    {
        if (is_null($this->_real)) {
            $this->_real = new Printer($this->_name);
        }
    }
}


class ProxyMain
{

    public function run()
    {
        $p = new PrinterProxy("Alice");
        echo "现在的名字是：{$p->getPrinterName()}\n";
        $p->setPrinterName("Bob");
        echo "现在的名字是：{$p->getPrinterName()}\n";
        $p->printer("Hello,World");
    }
}


(new ProxyMain())->run();