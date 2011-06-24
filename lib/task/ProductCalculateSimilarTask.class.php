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

    $groups = $this->connection->query("SELECT * FROM `similar_product_group`")->fetchAll();
    foreach ($groups as $group)
    {
      if (!empty($group['products']))
      {
        //$this->makeManualGroup($group);
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

    foreach ($products as $product)
    {
      $sql = "INSERT INTO `similar_product` (`master_id`, `slave_id`) VALUES (".$product.", ".implode("), (".$product.", ", array_diff($products, array($product, ))).");";
      $this->connection->query($sql);
    }
  }

  protected function makeAutoGroup($group)
  {
    $sql = "SELECT `property_id` FROM `similar_product_property` WHERE `group_id` = ".$group['id'];
    $properties = $this->connection->query($sql)->fetchAll();
    foreach ($properties as &$property)
    {
      $property = $property['property_id'];
    }
    $products = $this->connection->query("SELECT `id` FROM `product` WHERE `type_id` = ".$group['product_type_id'])->fetchAll();

    foreach ($products as $product)
    {
      $main_properties = $this->connection->query("SELECT * FROM `product_property_relation` WHERE `product_id` = ".$product['id']." AND `property_id` IN (".implode(",", $properties).")")->fetchAll();
      myDebug::dump(array('id' => $product['id'], 'prop' => $main_properties));
      $sql = "SELECT `product`.`id` FROM `product` INNER JOIN `product_property_relation` WHERE ``";
    }

    foreach ($properties as $property)
    {

    }
  }
}
