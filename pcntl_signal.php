<?php
echo 123;
//-------------------------------------------------------
// php处理信号 基于ticks
// PHP的函数无法直接注册到操作系统信号设置中，所以pcntl信号
// 需要依赖tick机制,pcntl_signal的实现原理是，触发信号后先
// 将信号加入一个队列中。然后在PHP的ticks回调函数中不断检查
// 是否有信号，如果有信号就执行PHP中指定的回调函数，如果没
// 有则跳出函数。

// ticks=1表示每执行1行PHP代码就回调此函数。实际上大部分时间
// 都没有信号产生，但ticks的函数一直会执行。如果一个服务器
// 程序1秒中接收1000次请求，平均每个请求要执行1000行PHP代码。
// 那么PHP的pcntl_signal，就带来了额外的 1000 * 1000，也就是
// 100万次空的函数调用。这样会浪费大量的CPU资源
//--------------------------------------------------------

set_time_limit(0);

if (PHP_SAPI != 'cli') exit('只允许在cli模式下运行');

declare (ticks=1);

function handler($singo)
{

    switch ($singo) 
    {
        case SIGUSR1:
            $msg = date('Y-m-d-H-i-s').'接收到信号'.SIGUSR1."[start]";
            break;

        case SIGUSR2:
            $msg = date('Y-m-d-H-i-s').'接收到信号'.SIGUSR2;
            break;

        case SIGINT:    
            $msg = date('Y-m-d-H-i-s').'接收到信号'.SIGINT."[CTRL+C]";
            break;

        default:
            $msg = date('Y-m-d-H-i-s')."接收到其他信号";
            break;
    }

    if (!empty($msg)){
        logger($msg);
    }
    
}


function logger($msg)
{
    $path = '/tmp/signal.log';
    return exec('echo '.$msg.' >> '.$path);
}

//安装了一个SIGUSR1信号触发器
pcntl_signal(SIGUSR1, 'handler');
//安装了一个SIGUSR2信号触发器
pcntl_signal(SIGUSR2, 'handler');

//安装了一个SIGKILL信号触发器
//SIGKILL是发送到处理的信号以使其立即终止。当发送到程序，
//SIGKILL使其立即终止。在对比SIGTERM和SIGINT，这个信号不
//能被捕获或忽略，并且在接收过程中不能执行任何清理在接收到该信号。
//所以不用安装触发器，避免PHP WARNING
#pcntl_signal(SIGKILL, 'handler');

//安装中断信号 终端在用户按下CTRL+C发送到前台进程 
//注意：程序有死循环，是杀不死的
pcntl_signal(SIGINT, 'handler');

while(true){

    //处理业务逻辑
    usleep(500000);
} 