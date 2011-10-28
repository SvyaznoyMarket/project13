#!/usr/bin/php -q
<?php

$pid = pcntl_fork();

if ($pid == -1)
{
  //ошибка
}
elseif ($pid)
{
  //сюда попадет родительский процесс
  if (isDaemonActive('/tmp/'.basename(__FILE__).'.pid'))
  {
    echo 'Daemon already active';
  }

  exit();
}
else
{
  //а сюда - дочерний процесс
  file_put_contents('/tmp/my_pid_file.pid', getmypid());

  while (true)
  {
    execute();

    sleep(3);
  }
}

posix_setsid();


/**
 *
 * @param string $pid_file
 * @return boolean
 */
function isDaemonActive($pid_file)
{
  if (is_file($pid_file))
  {
    $pid = file_get_contents($pid_file);
    //проверяем на наличие процесса
    if (posix_kill($pid, 0))
    {
      //демон уже запущен
      return true;
    }
    else
    {
      //pid-файл есть, но процесса нет
      if (!unlink($pid_file))
      {
        //не могу уничтожить pid-файл. ошибка
        exit(-1);
      }
    }
  }
  return false;
}


function execute()
{
  file_put_contents('/opt/WWWRoot/green.testground.ru/wwwroot/log/daemon.log', "+\n");
}