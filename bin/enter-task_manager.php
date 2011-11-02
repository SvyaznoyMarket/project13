#!/usr/bin/php -q
<?php

$pidFile = '/tmp/enter-task_manager.pid';
$delay = 8; //sec

$pid = pcntl_fork();

if (isDaemonActive($pidFile))
{
  echo "Daemon already active\n";
  exit();
}

file_put_contents($pidFile, getmypid());

if ($pid == -1)
{
  echo "Critical error: get no pid\n";
  exit();
}
elseif ($pid)
{
  //сюда попадет родительский процесс
  exit();
}
else
{
  //а сюда - дочерний процесс
  while (true)
  {
    shell_exec('cd /opt/WWWRoot/green.testground.ru/wwwroot && php symfony task-manager:run >> /dev/null');
    sleep($delay);
  }
}

posix_setsid();


/**
 *
 * @param string $pidFile
 * @return boolean
 */
function isDaemonActive($pidFile)
{
  if (is_file($pidFile))
  {
    $pid = file_get_contents($pidFile);
    //проверяем на наличие процесса
    if (posix_kill($pid, 0))
    {
      //демон уже запущен
      return true;
    }
    else
    {
      //pid-файл есть, но процесса нет
      if (!unlink($pidFile))
      {
        //не могу уничтожить pid-файл. ошибка
        exit(-1);
      }
    }
  }
  return false;
}
