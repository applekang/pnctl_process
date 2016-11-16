<?php

//基于pcntl_signal_dispatch 处理信号，性能强于 pcntl_signal

set_time_limit(0);

if (PHP_SAPI != 'cli') exit('只允许在cli模式下运行');


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
    $path = '/tmp/signal_dispatch.log';
    return exec('echo '.$msg.' >> '.$path);
}



while(true){

    if (empty($argv[1]))
    {
        usleep(50000);
        
    } else {

        switch ($argv[1]) {
            case 'sigusr1':
                //安装信号处理器
                pcntl_signal(SIGUSR1,'handler');
                //创建信号
                posix_kill(posix_getpid(), SIGUSR1);
                //分发
                pcntl_signal_dispatch();
                break;

            case 'sigusr2':
                pcntl_signal(SIGUSR2,'handler');
                posix_kill(posix_getpid(), SIGUSR2);
                pcntl_signal_dispatch();
                break;    
            
            default:
                echo "DONT’T TKNOW";
                break;
        }

        exit();

    }

}



