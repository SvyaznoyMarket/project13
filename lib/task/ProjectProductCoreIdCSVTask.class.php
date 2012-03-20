<?php

class ProjectProductCoreIdCSVTask extends sfBaseTask
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

    $this->namespace        = 'Project';
    $this->name             = 'ProductCoreIdCSV';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProjectProductCoreIdCSV|INFO] task does things.
Call it with:

  [php symfony ProjectProductCoreIdCSV|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    //для продуктов
    $productList = ProductTable::getInstance()->createBaseQuery()->fetchArray();
    $text = "id;core_id\n";
    foreach ($productList as $product) {
        $text .= $product['id'].";".$product['core_id']."\n";
    }
    unset($productList);
    file_put_contents('web/xml/product_core_id_relation.csv', $text);

    //для категорий продуктов
    $productCatList = ProductCategoryTable::getInstance()->createBaseQuery()->fetchArray();
    $text = "id;core_id\n";
    foreach ($productCatList as $productCat) {
      $text .= $productCat['id'].";".$productCat['core_id']."\n";
    }

    unset($productCatList);
    file_put_contents('web/xml/productCategory_core_id_relation.csv', $text);
  }
}
