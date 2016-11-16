<?php

//pcntl 创建多进程处理任务
set_time_limit(0);
$num = 4;

function logger($msg)
{
    $path = '/tmp/process.log';
    return exec('echo '.$msg.' >> '.$path);
}

$name = [];

$data = 100;//模拟个数字

while (true) {

    //假设数量超过每50个就开启一个进程

    $total_num = $data;

    $num = floor($data/50) - 1;//这是需要开启的进程数量


    for($i=0; $i<$num; $i++)
    {

        $pid = pcntl_fork();//创建一个进程

        if ($pid == -1)
        {
            //创建子进程失败
            $msg = date('Y-m-d-H-i-s').'进程['.posix_getpid().']创建子进程'.$i.'失败-----['.__FILE__.']';
        }

        elseif ($pid) 
        {    
            //父进程会得到子进程号，所以这里是父进程执行的逻辑
            pcntl_wait($status); //等待子进程中断，防止子进程成为僵尸进程。
        } 

        else 
        {
            //子进程的逻辑  子进程处理完应该退出
            exec('echo 进程号:'.posix_getpid().' >> 1.txt');
            sleep(5);
            exit();
        }
    }

    sleep(10);
}
