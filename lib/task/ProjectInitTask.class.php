<?php

class ProjectInitTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('packet_id', null, sfCommandOption::PARAMETER_REQUIRED, 'Packet id'),
      new sfCommandOption('sync_id', null, sfCommandOption::PARAMETER_REQUIRED, 'Sync id'),
      new sfCommandOption('status', null, sfCommandOption::PARAMETER_REQUIRED, 'Status'),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'init';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectInit|INFO] task does things.
Call it with:

  [php symfony ProjectInit|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $core = Core::getInstance();

    //myDebug::dump($options);
    $response = $core->query('load.get', array(
      'id' => $options['packet_id']
    ));


    myDebug::dump($response, false, 'yaml');
  }
}
