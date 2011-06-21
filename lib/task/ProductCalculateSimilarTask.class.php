<?php

class ProductCalculateSimilarTask extends sfBaseTask
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

    $this->namespace        = 'product';
    $this->name             = 'calculate-similar';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProductCalculateSimilar|INFO] task does things.
Call it with:

  [php symfony ProductCalculateSimilar|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here

    //myDebug::dump($connection); die();

    $groups = $this->connection->query("SELECT * FROM `similar_product_group`")->fetchAll();
    foreach ($groups as $group)
    {
      if (!empty($group['products']))
      {
        $this->makeManualGroup($group);
      }
      else
      {
        $this->makeAutoGroup($group);
      }
    }
  }

  protected function makeManualGroup($group)
  {
    if (!isset($group['products']) || empty($group['products']))
    {
      return false;
    }
    $products = split(",", $group['products']);
    foreach ($products as &$product)
    {
      $product = (int)trim($product);
    }
    unset($product);
    myDebug::dump($products);
    foreach ($products as $product)
    {
      $sql = "INSERT INTO `similar_product` (`master_id`, `slave_id`) VALUES (".$product.", ".implode("), (".$product.", ", array_diff($products, array($product, ))).");";
      $this->connection->query($sql);
    }
  }

  protected function makeAutoGroup()
  {

  }
}
