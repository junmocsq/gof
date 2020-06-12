<?php
// 在visitor模式中，数据结构和处理被分开来。我们编写一个表示“访问者”的类来访问数据结构中的元素，并把对各元素的处理交给访问者类。这样，当需要增加新的处理时，我们只需要编写新的访问者，然后让数据可以接受新的访问即可。

// 对于VDirectory类的实例和VFile类的实例，我们调用他们的accept方法
// 对于每一个VDirectory类的实例和VFile类的实例，我们只调用一次它们的accept方法
// 对于ListVisitor的实例，我们调用了它的visit(VDirectory)和visit(VFile)方法。
// 处理visit(VDirectory)和visit(VFile)的是同一个ListVisitor的实例

// Visitor访问者：负责对数据结构中每个具体元素（ConcreteElement角色）声明一个用来访问XXXX的visit(XXXX)方法。visit(XXXX)是用来处理XXXX的方法，负责实现该方法的是ConcreteVisitor角色。在示例中，由Visitor类扮演Visitor角色。
// ConcreteVisitor具体的访问者：负责实现Visitor角色所定义的接口。它要实现所有的visit(XXXX)方法，即实现如何处理ConcreteElement角色[ps:php没有参数重载，采用类判断代替]。在示例程序中，由ListVisitor类扮演此角色。如同在ListVisitor中，currentDir字段的值不断发生变化，随着visit(XXXX)处理的进行，ConcreteVisitor角色内部状态也会不断地发生变化。
// Element元素：Element角色表示Visitor角色的访问对象。它声明了接受访问者的accept方法，accept方法接收到的参数是Visitor角色。在示例程序中，由Element决口扮演此角色。
// ConcreteElement：负责实现Element角色所定义的接口。在示例程序中，由VFile类和VDirectory类扮演此角色。
// ObjectStructure对象结构：负责处理Element角色的集合。ConcreteVisitor角色为每个Element角色都准备了处理方法。在示例程序中，由VDirectory类扮演此角色（一人分饰两角）。为了让ConcreteVisitor角色可以遍历处理每个Element角色，在示例程序中，我们的VDirectory类中实现了iterator方法。


// 表示访问者的抽象类
abstract class Visitor
{
    abstract public function visit(VEntry $entry);
}

// 接受访问者的访问的接口
interface Element
{
    public function accept(Visitor $v);
}

abstract class VEntry implements Element
{
    abstract public function getName(): string;

    abstract public function getSize(): int;

    public function add(VEntry $entry)
    {
        throw new Exception("禁止直接调用add");
    }

    public function iterator()
    {
        throw new Exception("禁止直接调用iterator");
    }

    public function __toString()
    {
        return "{$this->getName()} ({$this->getSize()})";
    }
}

class VFile extends VEntry
{
    private string $_name;
    private int $_size;

    public function __construct(string $name, int $size)
    {
        $this->_name = $name;
        $this->_size = $size;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function getSize(): int
    {
        return $this->_size;
    }

    public function accept(Visitor $v)
    {
        $v->visit($this);
    }
}

class VDirectory extends VEntry
{
    private string $_name;
    private array $_dirArr = [];

    public function __construct(string $name)
    {
        $this->_name = $name;
    }

    public function add(VEntry $entry)
    {
        $this->_dirArr[] = $entry;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function iterator()
    {
        return $this->_dirArr;
    }

    public function getSize(): int
    {
        $sizeVisitor = new SizeVisitor();
        $this->accept($sizeVisitor);
        return $sizeVisitor->getSize();
    }

    public function accept(Visitor $v)
    {
        $v->visit($this);
    }
}

// visitor的子类，它的作用是访问数据结构并显示元素一览。
// accept方法调用visit方法，visit调用accept方法，这样就形成了非常复杂的递归调用。通常的递归方法是某个方法调用自身，在Visitor中，则是accept方法与visit方法之间的递归调用。
class ListVisitor extends Visitor
{
    private string $_currentDir = ""; // 当前正在访问的文件夹的名字

    public function visit(VEntry $entry)
    {
        if ($entry instanceof VFile) {
            return $this->_visitFile($entry);
        } elseif ($entry instanceof VDirectory) {
            return $this->_visitDirectory($entry);
        }
    }

    private function _visitFile(VFile $file)
    {
        echo $this->_currentDir . "/" . $file . "\n";
    }

    private function _visitDirectory(VDirectory $directory)
    {
        echo $this->_currentDir . "/" . $directory . "\n";
        $saveDir = $this->_currentDir;
        $this->_currentDir .= "/" . $directory->getName();
        foreach ($directory->iterator() as $entry) {
            $entry->accept($this);
        }
        $this->_currentDir = $saveDir;
    }
}


class FileFindVisitor extends Visitor
{
    private array $_fileArr = [];

    public function visit(VEntry $entry)
    {
        if ($entry instanceof VFile) {
            return $this->_visitFile($entry);
        } elseif ($entry instanceof VDirectory) {
            return $this->_visitDirectory($entry);
        }
    }

    private function _visitFile(VFile $file)
    {
        if (strpos($file->getName(), ".html") !== false) {
            $this->_fileArr[] = (string)$file;
        }
    }

    private function _visitDirectory(VDirectory $directory)
    {
        foreach ($directory->iterator() as $entry) {
            $entry->accept($this);
        }
    }

    public function getFoundFiles()
    {
        return $this->_fileArr;
    }
}

class SizeVisitor extends Visitor
{
    private $_size = 0;

    public function visit(VEntry $entry)
    {
        if ($entry instanceof VFile) {
            return $this->_visitFile($entry);
        } elseif ($entry instanceof VDirectory) {
            return $this->_visitDirectory($entry);
        }
    }

    private function _visitFile(VFile $file)
    {
        $this->_size += $file->getSize();
    }

    private function _visitDirectory(VDirectory $directory)
    {
        foreach ($directory->iterator() as $entry) {
            $entry->accept($this);
        }
    }

    public function getSize()
    {
        return $this->_size;
    }
}

class VisitorMain
{
    public function run()
    {
        echo "Making root entries...\n";
        $rootDir = new VDirectory("root");
        $binDir = new VDirectory("bin");
        $tmpDir = new VDirectory("tmp");
        $usrDir = new VDirectory("usr");

        $rootDir->add($binDir);
        $rootDir->add($tmpDir);
        $rootDir->add($usrDir);

        $binDir->add(new VFile("vi", 10000));
        $binDir->add(new VFile("mongo", 20000));
        $rootDir->accept(new ListVisitor());

        echo "\n";
        echo "Making user entries...\n";
        $yuki = new VDirectory("yuki");
        $hanako = new VDirectory("hanako");
        $tomura = new VDirectory("tomura");
        $usrDir->add($yuki);
        $usrDir->add($hanako);
        $usrDir->add($tomura);
        $yuki->add(new VFile("diary.html", 100));
        $yuki->add(new VFile("composite.java", 200));
        $hanako->add(new VFile("index.html", 300));
        $tomura->add(new VFile("game.txt", 400));
        $file = new VFile("junk.mail", 500);
        $tomura->add($file);
        $rootDir->accept(new ListVisitor());

        echo "HTML files are:\n";
        $ffv = new FileFindVisitor();
        $rootDir->accept($ffv);
        foreach ($ffv->getFoundFiles() as $f) {
            echo "{$f}\n";
        }
    }
}

(new VisitorMain())->run();