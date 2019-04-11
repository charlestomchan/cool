<?php

namespace Cool\Http\Server;



use Cool\Foundation\Bean\AbstractObject;
use Cool\Foundation\Coroutine;
use Cool\Support\Process;

/**
 * Class HttpServer
 * @package Cool\Http\Server
 * @author charles <charlestomchan@gmail.com>
 */
class HttpServer extends AbstractObject
{

    /**
     * 主机
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * 端口
     * @var int
     */
    public $port = 9501;

    /**
     * 应用配置文件
     * @var string
     */
    public $configFile = '';

    /**
     * 运行参数
     * @var array
     */
    public $setting = [];

    /**
     * 默认运行参数
     * @var array
     */
    protected $_setting = [
        // 开启协程
        'enable_coroutine' => false,
        // 主进程事件处理线程数
        'reactor_num'      => 8,
        // 工作进程数
        'worker_num'       => 8,
        // 任务进程数
        'task_worker_num'  => 0,
        // PID 文件
        'pid_file'         => '/var/run/cool-httpd.pid',
        // 日志文件路径
        'log_file'         => '/tmp/cool-httpd.log',
        // 异步安全重启
        'reload_async'     => true,
        // 退出等待时间
        'max_wait_time'    => 60,
        // 开启后，PDO 协程多次 prepare 才不会有 40ms 延迟
        'open_tcp_nodelay' => true,
        // 进程的最大任务数
        'max_request'      => 0,
    ];

    /**
     * 服务器
     * @var \Swoole\Http\Server
     */
    protected $_server;

    /**
     * 服务名称
     * @var string
     */
    const SERVER_NAME = 'cool-httpd';

    /**
     * 启动服务
     * @return bool
     */
    public function start()
    {
        // 初始化
        $this->_server = new \Swoole\Http\Server($this->host, $this->port);
        // 配置参数
        $this->_setting = $this->setting + $this->_setting;
        $this->_server->set($this->_setting);
        // 关闭内置协程
        $this->_server->set([
            'enable_coroutine' => false,
        ]);
        // 绑定事件
        $this->_server->on(SwooleEvent::START, [$this, 'onStart']);
        $this->_server->on(SwooleEvent::MANAGER_START, [$this, 'onManagerStart']);
        $this->_server->on(SwooleEvent::WORKER_START, [$this, 'onWorkerStart']);
        $this->_server->on(SwooleEvent::REQUEST, [$this, 'onRequest']);
        // 欢迎信息
        $this->welcome();
        // 启动
        return $this->_server->start();
    }

    /**
     * 主进程启动事件
     */
    public function onStart(\Swoole\Http\Server $server)
    {
        // 进程命名
        Process::setProcessTitle(static::SERVER_NAME . ": master {$this->host}:{$this->port}");
    }

    // 管理进程启动事件
    public function onManagerStart($server)
    {
        // 进程命名P
        Process::setProcessTitle(static::SERVER_NAME . ": manager");
    }

    /**
     * 工作进程启动事件
     */
    public function onWorkerStart(\Swoole\Http\Server $server, int $workerId)
    {
        // 进程命名
        if ($workerId < $server->setting['worker_num']) {
            Process::setProcessTitle(static::SERVER_NAME . ": worker #{$workerId}");
        } else {
            Process::setProcessTitle(static::SERVER_NAME . ": task #{$workerId}");
        }
        // 实例化App
        new \Cool\Http\Application(require $this->configFile);
    }

    /**
     * 请求事件
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        if ($this->_setting['enable_coroutine'] && Coroutine::id() == -1) {
            xgo(function () use ($request, $response) {
                call_user_func([$this, 'onRequest'], $request, $response);
            });
            return;
        }
        try {
            // 执行请求
            \Cool::$app->request->beforeInitialize($request);
            \Cool::$app->response->beforeInitialize($response);
            \Cool::$app->run();
        } catch (\Throwable $e) {
            \Cool::$app->error->handleException($e);
        }
        // 清扫组件容器
        if (!$this->_setting['enable_coroutine']) {
            \Cool::$app->cleanComponents();
        }
    }

    /**
     * 欢迎信息
     */
    protected function welcome()
    {
        $swooleVersion = swoole_version();
        $phpVersion    = PHP_VERSION;
        echo <<<EOL
        
        CCCCCCCCCCCCC                                  lllllll 
     CCC::::::::::::C                                  l:::::l 
   CC:::::::::::::::C                                  l:::::l 
  C:::::CCCCCCCC::::C                                  l:::::l 
 C:::::C       CCCCCC   ooooooooooo      ooooooooooo    l::::l 
C:::::C               oo:::::::::::oo  oo:::::::::::oo  l::::l 
C:::::C              o:::::::::::::::oo:::::::::::::::o l::::l 
C:::::C              o:::::ooooo:::::oo:::::ooooo:::::o l::::l 
C:::::C              o::::o     o::::oo::::o     o::::o l::::l 
C:::::C              o::::o     o::::oo::::o     o::::o l::::l 
C:::::C              o::::o     o::::oo::::o     o::::o l::::l 
 C:::::C       CCCCCCo::::o     o::::oo::::o     o::::o l::::l 
  C:::::CCCCCCCC::::Co:::::ooooo:::::oo:::::ooooo:::::ol::::::l
   CC:::::::::::::::Co:::::::::::::::oo:::::::::::::::ol::::::l
     CCC::::::::::::C oo:::::::::::oo  oo:::::::::::oo l::::::l
        CCCCCCCCCCCCC   ooooooooooo      ooooooooooo   llllllll
        

EOL;
        println('Server         Name:      ' . static::SERVER_NAME);
        println('System         Name:      ' . strtolower(PHP_OS));
        println("PHP            Version:   {$phpVersion}");
        println("Swoole         Version:   {$swooleVersion}");
        println('Framework      Version:   ' . \Cool::$version);
        $this->_setting['max_request'] == 1 and println('Hot            Update:    enabled');
        $this->_setting['enable_coroutine'] and println('Coroutine      Mode:      enabled');
        println("Listen         Addr:      {$this->host}");
        println("Listen         Port:      {$this->port}");
        println('Reactor        Num:       ' . $this->_setting['reactor_num']);
        println('Worker         Num:       ' . $this->_setting['worker_num']);
        println("Configuration  File:      {$this->configFile}");
    }

}