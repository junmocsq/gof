<?php
// 装饰边框与被装饰物的一致性
// 不断为对象添加装饰的设计模式被称为Decorator模式

// Component：增加功能时的核心角色。在示例中由DDisplay来扮演
// ConcreteComponent：该角色是实现了Component角色所定义的接口。在示例中，由StringDDisplay来扮演
// Decorator装饰物：该角色具有与Component角色相同的接口。在它内部保存了被装饰对象--Component角色。Decorator角色知道自己要装饰的对象。在示例程序中，由DBorder类扮演此角色。
// ConcreteDecorator具体的装饰物：该角色是具体的Decorator角色。在示例程序中，由SideBorder类和FullBorder类扮演Decorator模式的类。

abstract class DDisplay
{
    abstract public function getColumns();

    abstract public function getRows();

    abstract public function getRowText(int $row);

    final public function show()
    {
        for ($i = 0; $i < $this->getRows(); $i++) {
            echo $this->getRowText($i) . "\n";
        }
    }
}

class StringDDisplay extends DDisplay
{
    private string $_str;

    public function __construct($str)
    {
        $this->_str = $str;
    }

    public function getColumns()
    {
        return strlen($this->_str);
    }

    public function getRows()
    {
        return 1;
    }

    public function getRowText(int $row)
    {
        if ($row == 0) {
            return $this->_str;
        } else {
            return null;
        }
    }
}

class MultiStringDDisplay extends DDisplay
{
    private array $_strArr = [];
    // 当前数组中字符串的最大长度
    private int $_columns = 0;

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getRows()
    {
        return count($this->_strArr);
    }

    public function getRowText(int $row)
    {
        return $this->_strArr[$row] ?? "";
    }

    public function add(string $str)
    {
        $this->_strArr[] = $str;
        if (strlen($str) > $this->_columns) {
            $this->_columns = strlen($str);
        }
    }
}

abstract class DBorder extends DDisplay
{
    protected DDisplay $display;

    protected function __construct(DDisplay $display)
    {
        $this->display = $display;
    }
}

class SideBorder extends DBorder
{
    private string $_borderChar;

    public function __construct(DDisplay $display, string $char)
    {
        parent::__construct($display);
        $this->_borderChar = $char;
    }

    public function getColumns()
    {
        return $this->display->getColumns() + 2 * strlen($this->_borderChar);
    }

    public function getRows()
    {
        return $this->display->getRows();
    }

    public function getRowText(int $row)
    {
        $text = $this->display->getRowText($row);
        // 中间用空格补齐字符串
        return $this->_borderChar . $text . str_repeat(" ", $this->display->getColumns() - strlen($text)) . $this->_borderChar;
    }
}

class FullBorder extends DBorder
{
    public function __construct(DDisplay $display)
    {
        parent::__construct($display);
    }

    public function getColumns()
    {
        return $this->display->getColumns() + 2;
    }

    public function getRows()
    {
        return $this->display->getRows() + 2;
    }

    public function getRowText(int $row)
    {
        if ($row == 0) {
            return "+" . $this->_makeLine("-", $this->display->getColumns()) . "+";
        } elseif ($row == $this->display->getRows() + 1) {
            return "+" . $this->_makeLine("-", $this->display->getColumns()) . "+";
        } else {
            return "|" . $this->display->getRowText($row - 1) . "|";
        }
    }

    private function _makeLine($char, $count)
    {
        return str_repeat($char, $count);
    }
}

class UpDownBorder extends DBorder
{
    private string $_char;

    public function __construct(DDisplay $display, $char)
    {
        parent::__construct($display);
        $this->_char = $char;
    }

    public function getColumns()
    {
        return $this->display->getColumns();
    }

    public function getRows()
    {
        return $this->display->getRows() + 2;
    }

    public function getRowText(int $row)
    {
        if ($row == 0) {
            return $this->_makeLine($this->_char, $this->display->getColumns());
        } elseif ($row == $this->display->getRows() + 1) {
            return $this->_makeLine($this->_char, $this->display->getColumns());
        } else {
            return $this->display->getRowText($row - 1);
        }
    }

    private function _makeLine($char, $count)
    {
        return str_repeat($char, $count);
    }
}

class DecoratorMain
{
    public function run()
    {
        $d1 = new MultiStringDDisplay();
        $d1->add("zhangsan");
        $d1->add("lisi");
        $d1->add("wangxiaoer");
        $d2 = new UpdownBorder($d1, "$");
        $d3 = new SideBorder($d2, "*");
        $d1->show();
        echo "\n";
        $d2->show();
        echo "\n";
        $d3->show();
        echo "\n";

        $d4 = new SideBorder(
            new FullBorder(
                new FullBorder(
                    new SideBorder(new FullBorder($d3), "*")
                )
            ), "/"
        );
        $d4->show();
    }
}

(new DecoratorMain())->run();