<?php
// Mediator（仲裁者模式）：一方面，当发生麻烦事情的时候，通知仲裁者；当发生涉及全体组员的事情的时，也通知仲裁者。当仲裁者下达指示时，组员会立即执行。团队组员之间不再互相沟通并私自做出决定，而是发生任何事情都向仲裁者报告。另一方面，仲裁者站在整个团队的角度上对组员上报的事情做出决定。

// 要调整多个对象之间的关系时，就需要用到Mediator模式了。既不让各个对象之间相互通信，而是增加一个仲裁者角色，让他们各自与仲裁者通信。然后，将控制显示的逻辑处理交给仲裁者负责。

// Mediator仲裁者 中介者：负责定义与Colleague角色进行通信和做出决定的接口。在示例中，由Mediator类扮演
// ConcreteMediator具体的仲裁者：负责实现Mediator角色的接口，负责实际做出决定。在示例中，由LoginFrame类扮演
// Colleague同事：Colleague角色负责定义与Mediator角色通信的接口。在示例中，由Colleague类扮演
// ConcreteColleague具体的同事：ConcreteColleague角色负责实现Colleague角色的接口。在示例中，由ColleagueButton、ColleagueTextField、ColleagueCheckBox类扮演此角色。


interface Mediator
{
    public function createColleagues();

    public function colleagueChanged();
}

// 向仲裁者进行报告的组员的接口
interface Colleague
{
    public function setMediator(Mediator $mediator);

    public function setColleagueEnabled(bool $enabled);
}

class ColleagueButton implements Colleague
{

    private Mediator $_mediator;
    private string $_caption;
    private bool $_enabled = true;

    public function __construct($caption)
    {
        $this->_caption = $caption;
    }

    public function setMediator(Mediator $mediator)
    {
        $this->_mediator = $mediator;
    }

    public function setColleagueEnabled(bool $enabled)
    {
        $this->_enabled = $enabled;
    }
}

class ColleagueTextField implements Colleague
{

    private Mediator $_mediator;
    private string $_text;
    private int $_columns;

    private bool $_enabled;

    public function __construct($text, $columns)
    {
        $this->_text = $text;
        $this->_columns = $columns;
    }

    public function setMediator(Mediator $mediator)
    {
        $this->_mediator = $mediator;
    }

    public function setColleagueEnabled(bool $enabled)
    {
        $this->_enabled = $enabled;
    }

    // 当文字发生变化时通知mediator
    public function textValueChanged()
    {
        $this->_mediator->colleagueChanged();
    }

    public function getText()
    {
        return $this->_text;
    }
}

class ColleagueCheckBox implements Colleague
{

    private Mediator $_mediator;

    private string $_caption;
    private string $_group;
    private bool $_state;

    private bool $_enabled;

    public function __construct(string $caption, string $group, bool $state)
    {
        $this->_caption = $caption;
        $this->_group = $group;
        $this->_state = $state;
    }

    public function setMediator(Mediator $mediator)
    {
        $this->_mediator = $mediator;
    }

    public function setColleagueEnabled(bool $enabled)
    {
        $this->_enabled = $enabled;
    }

    // 当状态发生变化时通知Mediator
    public function itemStateChanged()
    {
        $this->_mediator->colleagueChanged();
    }

    public function getState()
    {
        return $this->_state;
    }
}

class LoginFrame implements Mediator
{
    private $_checkBoxGuest;
    private $_checkBoxLogin;
    private $_textUser;
    private $_textPassword;
    private $_buttonOk;
    private $_buttonCancel;

    public function __construct($title)
    {
        echo $title . "\n";
    }

    public function createColleagues()
    {
        $this->_checkBoxGuest = new ColleagueCheckBox("Guest", "g", true);
        $this->_checkBoxLogin = new ColleagueCheckBox("Login", "g", false);
        $this->_textUser = new ColleagueTextField("", 10);
        $this->_textPassword = new ColleagueTextField("", 10);
        $this->_buttonOk = new ColleagueButton("Ok");
        $this->_buttonCancel = new ColleagueButton("Cancel");

        $this->_checkBoxGuest->setMediator($this);
        $this->_checkBoxLogin->setMediator($this);
        $this->_textUser->setMediator($this);
        $this->_textPassword->setMediator($this);
        $this->_buttonOk->setMediator($this);
        $this->_buttonCancel->setMediator($this);

    }

    public function colleagueChanged()
    {
        if ($this->_checkBoxGuest->getState()) {
            $this->_textUser->setColleagueEnabled(false);
            $this->_textPassword->setColleagueEnabled(false);
            $this->_buttonOk->setColleagueEnabled(false);
        } else {
            $this->_textUser->setColleagueEnabled(true);
            $this->_userPasswordChanged();
        }

    }

    private function _userPasswordChanged()
    {
        if (strlen($this->_textUser->getText()) > 0) {
            $this->_textPassword->setColleagueEnabled(true);
            if (strlen($this->_textPassword->getText()) > 0) {
                $this->_buttonOk->setColleagueEnabled(true);
            } else {
                $this->_buttonOk->setColleagueEnabled(false);
            }
        } else {
            $this->_textPassword->setColleagueEnabled(false);
            $this->_buttonOk->setColleagueEnabled(false);
        }
    }
}


class MediatorMain
{
    public function run()
    {
        $a = new LoginFrame("Mediator Sample");
        $a->createColleagues();
        $a->colleagueChanged();
    }
}


(new MediatorMain())->run();