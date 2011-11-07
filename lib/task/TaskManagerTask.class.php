<?php

class TaskManagerTask extends sfBaseTask
{
  protected
    $logger = null
  ;

  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'core'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
      // add your own options here
    ));

    $this->namespace        = 'task-manager';
    $this->name             = 'run';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [TaskManager|INFO] task does things.
Call it with:

  [php symfony TaskManager|INFO]
EOF;

    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/task_manager.log'));
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfConfig::set('sf_logging_enabled', $options['log']);

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here


    $task = $this->getRunningTask();
    if (!$task)
    {
      return true;
    }

    $stepCount = 1;
    if ('project.init' == $task->type)
    {
      $stepCount = 500;
    }

    for ($step = 0; $step < $stepCount; $step++)
    {
      $this->logSection($task->type, "#{$task->id} packet={$task->core_packet_id} ...");

      // приоритет реального времени
      $task->priority = 0;
      $task->save();

      $this->runTask(str_replace('.', ':', $task->type), array('task_id' => $task->id), array(
        'application' => $options['application'],
        'env'         => $options['env'],
        'connection'  => $options['connection'],
      ));

      if ('success' == $task->status)
      {
        $this->logSection($task->type, "#{$task->id} ... ok");
      }
      else if ('fail' == $task->status)
      {
        $this->logSection($task->type, "#{$task->id} ... fail", null, 'ERROR');
      }

      $task->setDefaultPriority();
      $task->save();
    }

    if ($task->attempt > 100)
    {
      $task->status = 'fail';
      $task->save();
    }

    $task->free(true);
    unset($task);
  }


  protected function getRunningTask()
  {
    return TaskTable::getInstance()->getRunning(array('with_minPriority' => true, 'check_zeroPriority' => true));
  }

  public function logSection($section, $message, $size = null, $style = 'INFO')
  {
    parent::logSection($section, $message, $size, $style);

    call_user_func_array(array($this->logger, 'ERROR' == $style ? 'err' : 'log'), array($section.' - '.$message));
  }
}
