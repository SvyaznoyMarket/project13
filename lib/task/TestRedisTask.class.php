<?php

class TestRedisTask extends sfBaseTask
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

    $this->namespace = 'test';
    $this->name = 'redis';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [TestRedis|INFO] task does things.
Call it with:

  [php symfony TestRedis|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here

    /*
      foreach (range(1, 5000) as $i)
      {
      $r = new Product();
      $r->fromArray(array(
      'token'       => uniqid(),
      'name'        => 'Товар '.uniqid(),
      'type_id'     => 1,
      'creator_id'  => 1,
      'price'  => rand(1000, 20000),
      'is_instock' => true,
      ));
      $r->save();
      }
     */

    $timer = sfTimerManager::getTimer('redis');

    $t = ProductTable::getInstance();
    foreach (range(21820, 21820 - 4000) as $id)
    {
      $r = $t->createQuery()
        ->where('id = ?', $id)
        //->setHydrationMode(Doctrine_Core::HYDRATE_ARRAY)
        ->useResultCache(true, null, 'product-'.$id)
        ->fetchOne()
      ;
    }

    $elapsedTime = $timer->getElapsedTime();

    $this->logSection('elapsed time', $timer->addTime());
  }

}
