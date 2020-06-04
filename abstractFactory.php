<?php
// 将关联零件组装成产品
// 抽象工厂的工作是将“抽象零件”组装成“抽象产品”。
// 我们不关心零件的具体实现，而是只关心接口（API）。我们仅使用该接口（API）将零件组装成为产品。

// 抽象产品AbstractProduct：负责定义AbstractFactory角色所生成的抽象零件和产品的接口。在示例里，由Link类、Tray类、和Page类扮演此角色。
// 抽象工厂AbstractFactory：负责定义用于生成抽象产品的接口。在示例中，由AbstractFactory扮演此角色。
// 委托者Client：client角色仅会调用AbstractFactory角色和AbstractProduct角色的接口来进行工作，对于具体的零件、产品和工厂一无所知。在示例中，由AbstractFactoryMain来扮演此角色。
// 具体产品ConcreteProduct：实现AbstractProduct角色的接口。在示例中，ListLink、ListPage、ListTray 和 TableLink、TablePage、TableTray。
// 具体工厂ConcreteFactory：负责实现AbstractFactory角色的接口。在示例中，ListFactory和TableFactory

// 在Abstract Factory增加具体的工厂是容易的。
// 难以增加新的零件。如果要在添加一个零件Pictures，那么在具体工厂中也要添加具体产品ListPictures、TablePictures。已经编写完成的具体工厂越多，修改的工作量就越大。

// 断言终止运行
assert_options(ASSERT_BAIL, 1);

// 抽象的零件
abstract class Item
{
    protected $caption; // 项目标题

    public function __construct(string $caption)
    {
        $this->caption = $caption;
    }

    abstract public function makeHTML();
}

// 抽象的零件类
abstract class Link extends Item
{
    protected $url;

    public function __construct(string $caption, string $url)
    {
        parent::__construct($caption);
        $this->url = $url;
    }
}

// 抽象的零件
// 表示一个或多个Link类和Tray类的容器
abstract class Tray extends Item
{
    protected $tray = [];

    public function __construct(string $caption)
    {
        parent::__construct($caption);
    }

    public function add(Item $item)
    {
        $this->tray[] = $item;
    }
}

// 抽象的产品
abstract class Page
{
    protected $title;
    protected $author;
    protected $content = [];

    public function __construct(string $title, string $author)
    {
        $this->title = $title;
        $this->author = $author;
    }

    public function add(Item $item)
    {
        $this->content[] = $item;
    }

    public function output()
    {
        $filename = "{$this->title}.html";
        $f = fopen($filename, "w");
        fwrite($f, $this->makeHTML());
        fclose($f);
    }

    abstract public function makeHTML();
}

// 抽象工厂
abstract class AbstractFactory
{
    public static function getFactory(string $classname): AbstractFactory
    {
        $factory = null;
        assert(class_exists($classname), "{$classname}不存在");
        $factory = new $classname();
        assert($factory instanceof AbstractFactory,
            "{$classname}不是AbstractFactory的子类");
        return $factory;
    }

    abstract public function createLink(string $caption, string $url): Link;

    abstract public function createTray(string $caption): Tray;

    abstract public function createPage(string $title, string $author): Page;

    public function createYahooPage()
    {
        $link = $this->createLink("Yahoo!", "http://www.yahoo.com/");
        $page = $this->createPage("Yahoo!", "Yahoo!");
        $page->add($link);
        return $page;
    }
}

// 该类并没有使用任何具体零件、产品和工厂
class AbstractFactoryMain
{
    public function run($factoryName = "ListFactory")
    {

        $factory = AbstractFactory::getFactory($factoryName);
        // Link
        $people = $factory->createLink("人民日报", "http://www.people.com.cn/");
        $gmw = $factory->createLink("公明日报", "http://www.gmw.cn/");

        $usYahoo = $factory->createLink("Yahoo!", "http://www.yahoo.com/");
        $jpYahoo = $factory->createLink("Yahoo!Japan", "http://www.yahoo.co.jp/");
        $excite = $factory->createLink("Excite", "http://www.excite.com/");
        $google = $factory->createLink("google", "http://google.com");

        // Tray
        $trayNews = $factory->createTray("日报");
        $trayNews->add($people);
        $trayNews->add($gmw);

        $trayYahoo = $factory->createTray("Yahoo!");
        $trayYahoo->add($usYahoo);
        $trayYahoo->add($jpYahoo);

        $traySearch = $factory->createTray("检索引擎");
        $traySearch->add($trayYahoo);
        $traySearch->add($excite);
        $traySearch->add($google);

        $page = $factory->createPage("LinkPage", "JUNMOCSQ");
        $page->add($trayNews);
        $page->add($traySearch);
        $page->output();

        $factory->createYahooPage()->output();
    }
}

class ListFactory extends AbstractFactory
{
    public function createLink(string $caption, string $url): Link
    {
        return new ListLink($caption, $url);
    }

    public function createTray(string $caption): Tray
    {
        return new ListTray($caption);
    }

    public function createPage(string $title, string $author): Page
    {
        return new ListPage($title, $author);
    }

}

class ListLink extends Link
{
    public function __construct(string $caption, string $url)
    {
        parent::__construct($caption, $url);
    }

    public function makeHTML()
    {
        return " <li><a href=\"{$this->url}\">{$this->caption}</a></li>\n";
    }
}

class ListTray extends Tray
{
    private $_str = "";

    public function __construct(string $caption)
    {
        parent::__construct($caption);
    }

    public function makeHTML()
    {
        $this->_str .= "<li>\n{$this->caption}\n";
        $this->_str .= "<ul>\n";
        foreach ($this->tray as $item) {
            $this->_str .= $item->makeHTML();
        }
        $this->_str .= "</ul>\n";
        $this->_str .= "</li>\n";
        return $this->_str;
    }
}

class ListPage extends Page
{
    public function __construct(string $title, string $author)
    {
        parent::__construct($title, $author);
    }

    public function makeHTML()
    {
        $str = "";
        $str .= "<html><head><title>{$this->title}</title></head>\n<body>\n";
        $str .= "<h1>{$this->title}</h1>\n";
        $str .= "<ul>\n";
        foreach ($this->content as $item) {
            $str .= $item->makeHTML();
        }
        $str .= "</ul>\n";
        $str .= "<hr><address>{$this->author}</address>";
        $str .= "</body></html>\n";
        return $str;
    }
}

class TableFactory extends AbstractFactory
{
    public function createLink(string $caption, string $url): Link
    {
        return new TableLink($caption, $url);
    }

    public function createTray(string $caption): Tray
    {
        return new TableTray($caption);
    }

    public function createPage(string $title, string $author): Page
    {
        return new TablePage($title, $author);
    }
}

class TableLink extends Link
{
    public function __construct(string $caption, string $url)
    {
        parent::__construct($caption, $url);
    }

    public function makeHTML()
    {
        return " <td><a href=\"{$this->url}\">{$this->caption}</a></td>\n";
    }
}

class TableTray extends Tray
{
    private $_str = "";

    public function __construct(string $caption)
    {
        parent::__construct($caption);
    }

    public function makeHTML()
    {
        $this->_str .= "<td>\n{$this->caption}\n";
        $this->_str .= "<table width='100%' border='1'><tr>\n";
        $this->_str .= "<td bgcolor='#cccccc' align='center' colspan='" . count($this->tray) . "'><b>{$this->caption}</b></td>";
        $this->_str .= "</tr>\n<tr>\n";
        foreach ($this->tray as $item) {
            $this->_str .= $item->makeHTML();
        }
        $this->_str .= "</tr></table>\n";
        $this->_str .= "</td>\n";
        return $this->_str;
    }
}

class TablePage extends Page
{
    public function __construct(string $title, string $author)
    {
        parent::__construct($title, $author);
    }

    public function makeHTML()
    {
        $str = "";
        $str .= "<html><head><title>{$this->title}</title></head>\n<body>\n";
        $str .= "<h1>{$this->title}</h1>\n";
        $str .= "<table width='80%' border='3'>\n";
        foreach ($this->content as $item) {
            $str .= "<tr>" . $item->makeHTML() . "</tr>";
        }
        $str .= "</table>\n";
        $str .= "<hr><address>{$this->author}</address>";
        $str .= "</body></html>\n";
        return $str;
    }
}

(new AbstractFactoryMain())->run();
(new AbstractFactoryMain())->run("TableFactory");