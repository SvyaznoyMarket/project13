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
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
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
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $table = TaskTable::getInstance();

    $task = $table->getRunning(array('with_minPriority' => true, 'check_zeroPriority' => true));
    if (false == $task)
    {
      $zeroPriorityTask = $table->getZeroPriority();
      $this->logger->log("#{$zeroPriorityTask->id} running now. Stop");

      return true;
    }

    $this->logger->log("{$task->type} #{$task->id} starting...");
    $this->logSection($task->type, "#{$task->id} starting...");

    // приоритет реального времени
    $task->priority = 0;

    $this->runTask(str_replace('.', ':', $task->type), array('task_id' => $task->id), array());
    $this->logSection($task->type, "#{$task->id} done");
  }
}
