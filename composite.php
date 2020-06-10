<?php
// 能够使容器与内容具有一致性，创造出递归结构的模式就是Composite模式
// HTML列表 ul ol dl和表格都可以用Composite模式表示

// Leaf树叶：表示“内容”的角色。在该角色中不能放入其他对象。在示例程序中，由File类扮演此角色。
// Composite复合物：表示容器的角色。可以放入Leaf角色和Composite角色，在示例程序中由CDirectory类扮演此角色。
// Component 使Leaf角色和Composite角色具有一致性的角色。Component角色是Leaf角色和Composite角色的父类。在示例程序中，由Entry扮演此角色。
// Client：使用Composite模式的角色。在示例程序中，由CompositeMain扮演此角色。

abstract class Entry
{
    abstract public function getName();

    abstract public function getSize();

    abstract public function setPrefixPath(string $path);

    abstract public function getPath();

    public function add(Entry $entry)
    {
        throw new Exception("不能添加无效entry");
    }

    public function printListSpace()
    {
        $this->printList("");
    }

    abstract protected function printList(string $prefix);

    public function __toString()
    {
        return $this->getName() . " ({$this->getSize()}) ";
    }
}

class File extends Entry
{
    protected $_name;
    private $_size;
    private $_path;


    public function __construct(string $name, int $size)
    {
        $this->_name = $name;
        $this->_size = $size;
    }

    public function setPrefixPath($path)
    {
        $this->_path = $path;
    }

    public function getPath()
    {
        return $this->_path . "/" . $this->_name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getSize()
    {
        return $this->_size;
    }

    protected function printList(string $prefix)
    {
        echo $prefix . "/" . $this . "\n";
    }
}

class CDirectory extends Entry
{
    private $_name;
    private $_directoryArr = [];
    private $_path;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function setPrefixPath($path)
    {
        $this->_path = $path;
    }

    public function getPath()
    {
        return $this->_path . "/" . $this->_name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getSize()
    {
        $size = 0;
        foreach ($this->_directoryArr as $entry) {
            $size += $entry->getSize();
        }
        return $size;
    }

    public function add(Entry $entry)
    {
        $entry->setPrefixPath($this->_path . "/" . $this->_name);
        $this->_directoryArr[] = $entry;
        return $this;
    }

    protected function printList(string $prefix)
    {
        echo "{$prefix}/{$this}\n";
        foreach ($this->_directoryArr as $entry) {
            $entry->printList("{$prefix}/{$this->_name}");
        }
    }
}

class CompositeMain
{
    public function run()
    {
        echo "Making root entries...\n";
        $rootDir = new CDirectory("root");
        $binDir = new CDirectory("bin");
        $tmpDir = new CDirectory("tmp");
        $usrDir = new CDirectory("usr");

        $rootDir->add($binDir);
        $rootDir->add($tmpDir);
        $rootDir->add($usrDir);

        $binDir->add(new File("vi", 10000));
        $binDir->add(new File("mongo", 20000));
        $rootDir->printListSpace();
        echo "\n";
        echo "Making user entries...\n";
        $yuki = new CDirectory("yuki");
        $hanako = new CDirectory("hanako");
        $tomura = new CDirectory("tomura");
        $usrDir->add($yuki);
        $usrDir->add($hanako);
        $usrDir->add($tomura);
        $yuki->add(new File("diary.html", 100));
        $yuki->add(new File("composite.java", 200));
        $hanako->add(new File("memo.txt", 300));
        $tomura->add(new File("game.txt", 400));
        $file = new File("junk.mail", 500);
        $tomura->add($file);
        var_dump($file->getPath());
        $rootDir->printListSpace();
    }
}

(new CompositeMain())->run();