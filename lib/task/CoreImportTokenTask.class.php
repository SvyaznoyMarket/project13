<?php

class CoreImportTokenTask extends sfBaseTask
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
      new sfCommandOption('limit', null, sfCommandOption::PARAMETER_REQUIRED, 'Limit', 100),
      // add your own options here
    ));

    $this->namespace        = 'core';
    $this->name             = 'import-token';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [CoreImportToken|INFO] task does things.
Call it with:

  [php symfony CoreImportToken|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here

    foreach (array('ProductCategory' => 'product.category.get', 'Product' => 'product.get') as $model => $query)
    {
      $this->logSection('doctrine', 'import '.$model.'...');
      $this->importModel($model, $query, $options['limit']);
    }
  }

  public function importModel($model, $query, $limit = 10)
  {
    $core = Core::getInstance();
    $prefix = false;
    switch ($model)
    {
      case 'Product':
        $prefix = 'product';
        break;
      case 'ProductCategory':
        $prefix = 'catalog';
        break;
    }
    if (!$prefix) throw Exception('Prefix not sets');

    $offset = 0;
    $response = true;
    while ($response)
    {
      $response = $core->query($query, array(
        'start'  => $offset,
        'limit'  => $limit,
        'expand' => array(),
      ));

      if (!$response)
      {
        $error = $core->getError();
        if ($error)
        {
          $this->logSection('core', sfYaml::dump($error), null, 'ERROR');
        }
      }
      else {
        foreach ($response as $data)
        {
          $core_id = $data['id'];
          if (empty($core_id)) continue;

          $link = trim(preg_replace('/^\/'.$prefix.'/', '', $data['link']), '/');
          $v = explode('/', $link);

          $token = array_pop($v);
          $token_prefix = count($v) ? array_shift($v) : null;

          $q = Doctrine_Core::getTable($model)->createQuery()
            ->update($model)
            ->set('token', '?', $token)
            ->set('token_prefix', '?', $token_prefix)
            ->where('core_id = ?', $core_id)
          ;

          try {
            $q->execute();
          }
          catch (Exception $e)
          {
            echo "\n";
            $this->logSection($model, "core_id: $core_id, token: $token, token_prefix: $token_prefix", null, 'ERROR');
            $this->logSection('doctrine', $e->getMessage(), null, 'ERROR');
            echo "\n";
          }

          echo '.';
        }
      }

      $offset += $limit;

      echo "\n";
      $this->logSection('doctrine', "$offset records of $model imported");
      echo "\n";
    }
  }
}
