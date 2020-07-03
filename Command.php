<?php
// 命令也是类

// Command命令：负责定义命令的接口：由Command接口扮演
// ConcreteCommand具体的命令：负责实现Command定义的接口。由MacroCommand类和DrawCommand类扮演
// Receiver接受者：是Command角色执行命令时的对象，也可以称为命令接收者。在示例程序中，由DrawCanvas类接收DrawCommand的命令。
// Client请求者：Client角色负责生成ConcreteCommand角色并分配Receiver角色。在示例中，由CommandMain类扮演。
// Invoker发动者：开始执行命令的角色，它会调用在Command角色中定义的接口。在实例中，由CommandMain类和DrawCanvas类扮演此角色。

interface Command
{
    public function execute();
}

class MacroCommand implements Command
{
    private $_commands = [];

    public function execute()
    {
        foreach ($this->_commands as $command){
            $command->execute();
        }
    }

    public function append(Command $command)
    {
        $this->_commands[] = $command;
    }

    public function undo()
    {
        if ($this->_commands)
            array_pop($this->_commands);
    }

    public function clear()
    {
        $this->_commands = [];
    }
}

class DrawCommand implements Command
{
    private $_position = "";
    private $_name = "";

    public function __construct($position, $name)
    {
        $this->_name = $name;
        $this->_position = $position;
    }

    public function execute()
    {
        echo "画点，name:{$this->_name} position:{$this->_position}\n";
    }
}

class DrawCanvas
{
    private $_color;
    private $_name;
    private $_history;

    public function __construct($color, $name, MacroCommand $history)
    {
        $this->_name = $name;
        $this->_color = $color;
        $this->_history = $history;
        echo "画部生成，name:{$this->_name} color:{$this->_color}\n";
    }

    public function paint(){
        $this->_history->execute();
    }

}


class CommandMain{
    public function run(){
        $history = new MacroCommand();
        $drawCanvas = new DrawCanvas("red","画布",$history);

        $draw = new DrawCommand("1","点");
        $history->append($draw);
        $draw->execute();

        $draw = new DrawCommand("2","点");
        $history->append($draw);
        $draw->execute();

        $draw = new DrawCommand("3","点");
        $history->append($draw);
        $draw->execute();

        $draw = new DrawCommand("4","点");
        $history->append($draw);
        $draw->execute();


        $drawCanvas->paint();
        $history->undo();
        $drawCanvas->paint();
    }
}

(new CommandMain())->run();
