<?php

class DoctrineTestModelTask extends sfBaseTask
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
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'test-model';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [DoctrineTestModel|INFO] task does things.
Call it with:

  [php symfony DoctrineTestModel|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $models = Doctrine::loadModels(sfConfig::get('sf_lib_dir') . '/model');

    foreach ($models as $model)
    {
      if ((false !== strpos($model, 'Base')) || (false !== strpos($model, 'Table'))) continue;

      try
      {
        Doctrine_Core::getTable($model)->createQuery()->select('*')->limit(1)->execute();
        $this->logSection($model, 'ok');
      }
      catch (Exception $e) {
        $this->logSection($model, 'fail');
        $this->logBlock($e->getMessage(), 'ERROR');
      }
    }
  }
}
