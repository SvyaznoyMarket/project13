<?php

class ProjectTestBuildTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'test-build';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectTestBuild|INFO] task does things.
Call it with:

  [php symfony myProjectTestBuild|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here

    foreach (array(
      'doctrine:build'          => array(
        array(),
        array('all' => true, 'no-confirmation' => true, 'and-load' => true, 'application' => 'main'),
      ),
      'doctrine:test-data-load' => array('application' => 'main'),
      'cache:clear'             => array(),
      'doctrine:test-model'     => array('application' => 'main'),
    ) as $name => $params)
    {
      $this->runTask($name, isset($params[0]) ? $params[0] : array(), isset($params[1]) ? $params[1] : array());
    }

    $this->logSection('redis-', shell_exec('redis-cli FLUSHALL'));
  }
}
