<?php
/**
 * 迭代器模式
 * 不要只使用具体类来编程，要优先使用抽象类和接口来编程。
 * 迭代器（Iterator）：负责定义逐个遍历元素的接口
 * 具体的迭代器(ConcreteIterator)：实现Iterator角色的接口
 * 集合(Aggregate)：定义创建Iterator角色的接口
 * 具体的集合(ConcreteAggregate)：实现Aggregate角色所定义的接口
 */

// 集合的接口
interface Aggregate
{
    public function iterator(): BIterator;
}

// 遍历结合的接口
interface  BIterator
{
    // 确认接下来可以调用next
    public function hasNext(): bool;

    // 返回当前元素，并指向下一个
    public function next();
}


// 书类
class Book
{
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }
}

// 书架类 ConcreteAggregate
class BookShelf implements Aggregate
{
    private $_books;
    private $_last = 0;

    public function getBookAt(int $index): Book
    {
        return $this->_books[$index];
    }

    public function appendBook(Book $book)
    {
        $this->_books[$this->_last] = $book;
        $this->_last++;
    }

    public function getLength()
    {
        return $this->_last;
    }

    public function iterator(): BIterator
    {
        return new BookShelfIterator($this);
    }

}

// 书架迭代器
class BookShelfIterator implements BIterator
{
    private $_bookShelf;
    private $_index;

    public function __construct(BookShelf $shelf)
    {
        $this->_bookShelf = $shelf;
        $this->_index = 0;
    }

    public function hasNext(): bool
    {
        if ($this->_index < $this->_bookShelf->getLength()) {
            return true;
        } else {
            return false;
        }
    }

    public function next(): Book
    {
        $book = $this->_bookShelf->getBookAt($this->_index);
        $this->_index++;
        return $book;
    }
}


class BookMain
{
    public function run()
    {
        $bookShelf = new BookShelf();
        $bookShelf->appendBook(new Book("Around the word In 80 Days"));
        $bookShelf->appendBook(new Book("Bible"));
        $bookShelf->appendBook(new Book("Cinderella"));
        $bookShelf->appendBook(new Book("Daddy-Long-Legs"));

        $iterator = $bookShelf->iterator();
        while ($iterator->hasNext()) {
            var_dump($iterator->next()->getName());
        }

    }
}

(new BookMain())->run();

