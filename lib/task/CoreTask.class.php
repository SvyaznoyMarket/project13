<?php

class CoreTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('query', sfCommandArgument::REQUIRED, 'Query'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'core'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev_green'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('param', null, sfCommandOption::PARAMETER_REQUIRED, 'Query parameters', '[]'),
      new sfCommandOption('data', null, sfCommandOption::PARAMETER_REQUIRED, 'Query data', '[]'),
      new sfCommandOption('view', null, sfCommandOption::PARAMETER_REQUIRED, 'View', 'yaml'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'core';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [Core|INFO] task does things.
Call it with:

  [php symfony Core|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $encode = 'koi8-r';

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $core = Core::getInstance();
    $response = $core->query($arguments['query'], sfYaml::load($options['param']), sfYaml::load($options['data']));

    if (!$response)
    {
      $error = $core->getError();
      $this->logSection('core', is_array($error) ? "\n".iconv('utf-8', $encode, sfYaml::dump($error)) : $error, null, 'ERROR');
    }
    else {
      $this->logSection('core', 'response', null, 'INFO');

      switch ($options['view'])
      {
        case 'yaml': case 'yml':
          $response = sfYaml::dump($response, 6);
          break;
        case 'json':
          $response = json_encode($response);
          break;
      }

      myDebug::dump(iconv('utf-8', $encode, $response));
    }
  }
}
