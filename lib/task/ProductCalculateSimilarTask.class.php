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
    $properties = $this->connection->query($sql)->fetchAll(Doctrine::FETCH_ASSOC);
    foreach ($properties as &$property)
    {
      $property = $property['property_id'];
    }
    unset($property);
    $products = $this->connection->query("SELECT `id` FROM `product` WHERE `type_id` = ".$group['product_type_id'])->fetchAll(Doctrine::FETCH_ASSOC);

    foreach ($products as $product)
    {
      $products_properties = $this->connection->query("SELECT `productProperty`.*, `property`.`type` FROM `product_property_relation` AS `productProperty` INNER JOIN `product_property` AS `property` ON `property`.`id` = `productProperty`.`property_id` WHERE `product_id` = ".$product['id']." AND `property_id` IN (".implode(",", $properties).")")->fetchAll(Doctrine::FETCH_ASSOC);

      $properties_to_compare = array();
      foreach ($products_properties as &$property)
      {
        if (!isset($properties_to_compare[$property['property_id']]))
        {
          $properties_to_compare[$property['property_id']] = array('type' => $property['type'], 'values' => array(), );
        }
        $properties_to_compare[$property['property_id']]['values'][] = strcmp($property['type'], 'select') ? (is_null($property['value_'.$property['type']]) ? "`value_".$property['type']."` IS NULL" : "`value_".$property['type']."` = '".addcslashes($property['value_'.$property['type']])."'") : "option_id = ".$property['option_id'];//strcmp($property['type'], 'select') ? (is_null($property['value_'.$property['type']]) ? "NULL" : "'".addcslashes($property['value_'.$property['type']])."'") : $property['option_id'];//empty($property['value'.$property['type']]) ? $property['value'] : $property['value'.$property['type']];
      }

      $sql_if_section = "";
      foreach ($properties_to_compare as $id => $property)
      {
        $sql_if_section .= (strlen($sql_if_section) ? " OR " : "")."(property_id = ".$id." AND (".implode(" OR ", $property['values'])."))";
      }
      $sql = "SELECT `product`.`id`, SUM(IF((".$sql_if_section."), 1, 0)) AS `matches` FROM `product` INNER JOIN `product_property_relation` ON `product`.`id` = `product_id` WHERE `product`.`id` <> ".$product['id']." GROUP BY `product`.`id` HAVING `matches` >= ".$group['match'];
      $similar_products = $this->connection->query($sql)->fetchAll(Doctrine::FETCH_ASSOC);
      $sql_insert = "";
      foreach ($similar_products as $similar_product)
      {
        if (strlen($sql_insert))
        {
          $sql_insert .= ", (".$product['id'].", ".$similar_product['id'].")";
        }
        else
        {
          $sql_insert = "INSERT IGNORE INTO `similar_product` (`master_id`, `slave_id`) VALUES (".$product['id'].", ".$similar_product['id'].")";
        }
      }
      $this->connection->query($sql_insert);
    }

  }
}
