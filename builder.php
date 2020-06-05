<?php
// 组装复杂的实例
// Builder建造者：负责定义用于生成实例的接口。Builder角色中准备了用于生成实例的方法
// ConcreteBuilder具体的建造者：负责实现Builder角色的接口的类。
// Director监工：负责使用Builder角色的接口来实现实例。他不依赖于ConcreteBuilder角色。它只调用Builder角色中被定义的方法


// 声明编写文档的抽象类
abstract class Builder
{
    abstract public function makeTitle(string $title);

    abstract public function makeString(string $str);

    abstract public function makeItems(array $items);

    abstract public function close();
}


// 使用builder类中声明的方法来编写文档
class Director
{
    private $_builder;

    public function __construct(Builder $builder)
    {
        $this->_builder = $builder;
    }

    public function construct()
    {
        $this->_builder->makeTitle("Greeting");
        $this->_builder->makeString("从早上到下午");
        $this->_builder->makeItems([
            "早上好。",
            "下午好。"
        ]);
        $this->_builder->makeString("晚上");
        $this->_builder->makeItems([
            "晚上好。",
            "晚安。",
            "再见。"
        ]);
        $this->_builder->close();
    }
}

class HtmlBuilder extends Builder
{
    private $_filename;
    private $_writer;

    public function makeTitle(string $title)
    {
        $this->_filename = "{$title}.html";
        $this->_writer = fopen($this->_filename, "w");
        fwrite($this->_writer, "<html>
<head>
<title>{$title}</title>
</head>
<body>");
        fwrite($this->_writer, "<h1>{$title}</h1>
");
    }

    public function makeString(string $str)
    {
        fwrite($this->_writer, "<p>{$str}</p>
");
    }

    public function makeItems(array $items)
    {
        fwrite($this->_writer, "<ul>
");
        foreach ($items as $v) {
            fwrite($this->_writer, "<l1>{$v}</l1>
");
        }
        fwrite($this->_writer, "</ul>
");
    }

    public function close()
    {
        fwrite($this->_writer, "</body>
</html>");
        fclose($this->_writer);
    }

    public function getResult()
    {
        return $this->_filename;
    }
}

class TextBuilder extends Builder
{
    private $_buffer;

    public function makeTitle(string $title)
    {
        $this->_buffer .= "================================\n";
        $this->_buffer .= "「{$title}」\n\n";

    }

    public function makeString(string $str)
    {
        $this->_buffer .= "œ {$str}\n\n";
    }

    public function makeItems(array $items)
    {
        foreach ($items as $v) {
            $this->_buffer .= " .{$v}\n";
        }
        $this->_buffer .= "\n";
    }

    public function close()
    {
        $this->_buffer .= "================================\n";
    }

    public function getResult()
    {
        return $this->_buffer;
    }
}

class BuilderMain
{
    public function run()
    {
        $text = new TextBuilder();
        $d1 = new Director($text);
        $d1->construct();
        echo $text->getResult();
        $html = new HtmlBuilder();
        $d2 = new Director($html);
        $d2->construct();
        echo $html->getResult() . "文件编写完成";
    }
}

(new BuilderMain())->run();