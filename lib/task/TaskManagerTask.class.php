<?php

class TaskManagerTask extends sfBaseTask
{
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

    $this->namespace        = '';
    $this->name             = 'TaskManager';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [TaskManager|INFO] task does things.
Call it with:

  [php symfony TaskManager|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $list = TaskTable::getInstance()->getRunningList();
    if (isset($list[0]) && (0 === $list[0]->priority))
    {
      $list = TaskTable::getInstance()->createList(array($list[0]));
    }
    foreach ($list as $task)
    {
      $this->logSection($task->type, 'starting...');
      $this->runTask(str_replace('.', ':', $task->type), array(), $task->getContentData());
      $this->logSection($task->type, 'done');
    }
  }
}
