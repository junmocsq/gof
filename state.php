<?php
// State模式：用类来表示状态

// State状态：定义了根据不同状态进行不同处理的接口。该接口是那些处理内容依赖于状态的方法的集合。在示例中，由State接口扮演此角色
// ConcreteState具体状态：表示各个具体的状态，它实现了State接口。在示例程序中，由DayState类和NightState类扮演此角色。
// Context状况、前后关系、上下文：表示持有当前状态的ConcreteState角色。此外，它还定义了供外部调用者使用State模式的接口。在示例程序中，由Context接口和SafeFrame类扮演此角色。具体而言，Context接口定义了供外部调用者使用State模式的接口，而SafeFrame类则持有表示当前状态的ConcreteState角色。

// State模式用类表示系统的“状态”，并以此将复杂的程序分解开来


interface State
{
    // 设置时间
    public function doClock(Context $context, int $hour);

    // 使用金库
    public function doUse(Context $context);

    // 按下警铃
    public function doAlarm(Context $context);

    // 正常通话
    public function doPhone(Context $context);
}

interface Context
{
    // 设置时间
    public function setClock(int $hour);

    // 改变状态
    public function changeState(State $state);

    // 联系警报中心
    public function callSecurityCenter(string $msg);

    // 在警报中心留下记录
    public function recordLog(string $msg);
}

class DayState implements State
{

    private static $obj = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$obj)) {
            self::$obj = new self();
        }
        return self::$obj;
    }

    public function doClock(Context $context, int $hour)
    {
        if ($hour < 9 || $hour > 17) {
            $context->changeState(NightState::getInstance());
        }
    }

    public function doUse(Context $context)
    {
        $context->recordLog("使用【金库】（白天）");
    }

    public function doAlarm(Context $context)
    {
        $context->callSecurityCenter("按下警铃（白天）");
    }

    public function doPhone(Context $context)
    {
        $context->callSecurityCenter("正常通话（白天）");
    }

    public function __toString()
    {
        return "[白天]";
    }
}

class NightState implements State
{

    private static $obj = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$obj)) {
            self::$obj = new self();
        }
        return self::$obj;
    }

    public function doClock(Context $context, int $hour)
    {
        if ($hour >= 9 && $hour < 17) {
            $context->changeState(DayState::getInstance());
        }
    }

    public function doUse(Context $context)
    {
        $context->callSecurityCenter("紧急：晚上使用金库！");
    }

    public function doAlarm(Context $context)
    {
        $context->callSecurityCenter("按下警铃（晚上）");
    }

    public function doPhone(Context $context)
    {
        $context->recordLog("晚上通话录音");
    }

    public function __toString()
    {
        return "【晚上】";
    }
}

class SafeFrame implements Context
{

    private State $_state;

    public function __construct($title, State $state)
    {
        echo "【【{$title}】】\n";
        $this->_state = $state;
    }

    public function actionPerformed()
    {
        $this->_state->doUse($this);
        $this->_state->doAlarm($this);
        $this->_state->doPhone($this);
    }

    public function changeState(State $state)
    {
        echo "状态从{$this->_state}变为了{$state}\n";
        $this->_state = $state;
    }

    public function callSecurityCenter(string $msg)
    {
        echo $this->_wrapper("call! {$msg} \n");
    }

    public function setClock(int $hour)
    {
        $clockString = "现在的时间是：" . sprintf("%02d", $hour) . "\n";
        echo $clockString;
        echo $this->_wrapper($clockString);
        $this->_state->doClock($this, $hour);
    }

    public function recordLog(string $msg)
    {
        echo $this->_wrapper("record... {$msg} \n");
    }

    private function _wrapper($str)
    {
        return "\033[31m {$str} \033[0m";
    }
}


class StateMain
{
    public function run()
    {
        $frame = new SafeFrame("State Sample", NightState::getInstance());

        while (true) {
            for ($i = 0; $i < 24; $i++) {
                $frame->setClock($i);
                $frame->actionPerformed();
                usleep(1000000);

            }
        }
    }
}


(new StateMain())->run();