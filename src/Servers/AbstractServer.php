<?php
/**
 * @link http://ipaya.cn/
 * @copyright Copyright (c) 2016 ipaya.cn
 */

namespace iPaya\Swoole\Servers;

use iPaya\Swoole\Helpers\ConsoleHelper;
use Swoole\Server as SwooleServer;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\helpers\Console;

abstract class AbstractServer extends Object
{
    /**
     * @var string 服务器名称
     */
    public $name = '服务器';
    /**
     * @var string 绑定地址
     */
    public $host = '127.0.0.1';
    /**
     * @var int 绑定端口
     */
    public $port = 9051;
    /**
     * @var SwooleServer
     */
    public $server;
    /**
     * @var array Swoole 设置选项
     */
    public $swooleOptions = [];
    /**
     * @var array 需要设置的回调函数
     *
     * 回调函数包括: onStart, onWorkerStart, onConnect, onReceive, onClose
     */
    public $events = [

    ];

    public static $eventHandlers = [
        'start' => 'onStart',
        'shutdown' => 'onShutdown',
        'workerStart' => 'onWorkerStart',
        'workerStop' => 'onWorkerStop',
        'timer' => 'onTimer',
        'connect' => 'onConnect',
        'receive' => 'onReceive',
        'packet' => 'onPacket',
        'close' => 'onClose',
        'task' => 'onTask',
        'finish' => 'onFinish',
        'pipMessage' => 'onPipeMessage',
        'workerError' => 'onWorkerError',
        'managerStart' => 'onManagerStart',
        'managerStop' => 'onManagerStop',
    ];


    /**
     * 启动服务器
     */
    public function start()
    {
        $this->server = new SwooleServer($this->host, $this->port);
        $this->server->set($this->swooleOptions);

        foreach ($this->events as $event) {
            if (!isset(self::$eventHandlers[$event])) {
                throw new InvalidParamException("事件 \"{$event}\" 不存在。");
            } else {
                $this->server->on($event, [$this, self::$eventHandlers[$event]]);
            }
        }
        ConsoleHelper::stdout("启动 [{$this->name}] ...");
        $this->server->start();
    }

    /**
     * 关闭服务器
     */
    public function shutdown()
    {
        ConsoleHelper::stdout("关闭{$this->name}...");
    }

    /**
     * @param SwooleServer $server
     */
    public function onStart($server)
    {
        ConsoleHelper::stdout("[Bind on $this->host:$this->port] [Ok]" . PHP_EOL, Console::BOLD, Console::FG_GREEN);
    }

    /**
     * @param SwooleServer $server
     */
    public function onShutdown($server)
    {
        ConsoleHelper::stdout('[Ok]' . PHP_EOL, Console::BOLD, Console::FG_GREEN);
    }

    /**
     * @param SwooleServer $server
     * @param int $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        ConsoleHelper::stdout("Worker #{$worker_id} started." . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $worker_id
     */
    public function onWorkerStop($server, $worker_id)
    {
        ConsoleHelper::stdout('onWorkerStop' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $interval
     */
    public function onTimer($server, $interval)
    {
        ConsoleHelper::stdout('onTimer' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param int $from_id
     */
    public function onConnect($server, $fd, $from_id)
    {
        ConsoleHelper::stdout("#{$fd} connected." . $fd . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     */
    public function onReceive($server, $fd, $from_id, $data)
    {
        ConsoleHelper::stdout('Receive data from #' . $fd . "\n" . $data . PHP_EOL . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param string $data
     * @param array $client_info
     */
    public function onPacket($server, $data, array $client_info)
    {
        ConsoleHelper::stdout('onPacket' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($server, $fd, $reactorId)
    {
        ConsoleHelper::stdout("Client #{$fd} closed." . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $task_id
     * @param int $src_worker_id
     * @param mixed $data
     */
    public function onTask(SwooleServer $server, $task_id, $src_worker_id, $data)
    {
        ConsoleHelper::stdout('onTask $' . $task_id . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $task_id
     * @param string $data
     */
    public function onFinish($server, $task_id, $data)
    {
        ConsoleHelper::stdout("Task \${$task_id} Finished." . PHP_EOL . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $from_worker_id
     * @param string $message
     */
    public function onPipeMessage($server, $from_worker_id, $message)
    {
        ConsoleHelper::stdout('onPipeMessage' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     */
    public function onWorkerError($server, $worker_id, $worker_pid, $exit_code, $signal)
    {
        ConsoleHelper::stdout('onWorkerError' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     */
    public function onManagerStart($server)
    {
        ConsoleHelper::stdout('onManagerStart' . PHP_EOL);
    }

    /**
     * @param SwooleServer $server
     */
    public function onManagerStop($server)
    {
        ConsoleHelper::stdout('onManagerStop' . PHP_EOL);
    }
}
