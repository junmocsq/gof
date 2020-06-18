<?php
// 使用Facade模式可以为互相关联在一起的错综复杂的类整理出高层接口。其中的Facade角色可以让系统对外只有一个简单的接口。而且Facade角色还会考虑到系统内部各个类之间的责任关系和依赖关系

// Facade窗口：Facade角色是代表构成系统的许多其他角色的“简单窗口”。Facade角色向系统外部提供高层接口。在示例程序中，由PageMaker类扮演此角色。
// 构成系统的许多其他角色：这些角色各自完成自己的工作，它们并不知道Facade角色。Facade角色调用其他角色进行工作，但是其他角色不会调用Facade角色。在示例中，由Database类和HtmlWriter类扮演此角色。
// Client请求者：负责调用Facade角色

// Facade模式让复杂的东西看起来简单。“复杂的东西”是指在后台工作的这些类之间的关系和他们的使用方法。使用Facade模式可以让我们不必在意这些复杂的东西。
// 递归的使用Facade模式

class Database
{
    private function __construct() // 防止外部new出Database的实例
    {
    }

    public static function getProperties(string $dbname)
    {
        $filename = $dbname . ".txt";
        $prop = new Properties();
        $prop->load($filename);
        return $prop;
    }
}

class Properties
{
    private array $_database = [
        "maildata.txt" => [
            "hyuki@hyuki.com" => "Hiroshi Yuki",
            "hanako@hyuki.com" => "Hanako Sato",
            "tomura@hyuki.com" => "Tomura",
            "mamoru@hyuki.com" => "Mamoru Takahashi",
        ]
    ];
    private array $_property = [];


    public function load($filename)
    {
        $this->_property = $this->_database[$filename] ?? [];
    }

    public function getProperty($mailAddr)
    {
        return $this->_property[$mailAddr] ?? "";
    }
}

class HtmlWriter
{
    private $_writer;

    public function __construct($writer)
    {
        $this->_writer = $writer;
    }

    public function title(string $title)
    {
        fwrite($this->_writer, "<html>");
        fwrite($this->_writer, "<head>");
        fwrite($this->_writer, "<title>{$title}</title>");
        fwrite($this->_writer, "</head>");
        fwrite($this->_writer, "<body>\n");
        fwrite($this->_writer, "<h1>{$title}</h1>");
    }

    public function paragraph(string $msg)
    {
        fwrite($this->_writer, "<p>{$msg}</p>");
    }

    public function link(string $href, string $caption)
    {
        $this->paragraph("<a href='{$href}'>{$caption}</a>");
    }

    public function mailto(string $mailaddr, string $username)
    {
        $this->link("mailto:{$mailaddr}", $username);
    }

    public function close()
    {
        fwrite($this->_writer, "</body>");
        fwrite($this->_writer, "</html>\n");
        fclose($this->_writer);
    }
}

class PageMaker
{
    private function __construct()
    {
    }

    public static function makeWelcomePage(string $mailAddr, string $filename)
    {
        $mailProp = Database::getProperties("maildata");
        $username = $mailProp->getProperty($mailAddr);
        $writer = new HtmlWriter(fopen($filename, "w"));
        $writer->title("Welcome to {$username} 's page!");
        $writer->paragraph($username . " 欢迎来到{$username}的主页。 ");
        $writer->paragraph("等着你的邮件哦！");
        $writer->mailto($mailAddr, $username);
        $writer->close();
        echo "{$filename} is created for {$mailAddr} ($username)\n";
    }
}

class FacadeMain
{
    public function run()
    {
        PageMaker::makeWelcomePage("hyuki@hyuki.com", "welcome.html");
    }
}

(new FacadeMain())->run();