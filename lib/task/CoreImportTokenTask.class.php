<?php

class CoreImportTokenTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model'),
      new sfCommandArgument('query', sfCommandArgument::REQUIRED, 'The core query'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('offset', null, sfCommandOption::PARAMETER_REQUIRED, 'Offset', 0),
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

    $model = $arguments['model'];
    $query = $arguments['query'];

    $offset = $options['offset'];
    $limit = $options['limit'];

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

    while ($response = $core->query($query, array(
      'start'  => $offset,
      'limit'  => $limit,
      'expand' => array(),
    ))) {

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

          $record = Doctrine_Core::getTable($model)->createQuery()
            ->where('core_id = ?', $core_id)
            ->fetchOne()
          ;
          if (!$record)
          {
            echo '?';
            continue;
          }

          // redirect
          if ('ProductCategory' == $model)
          {
            $redirect = new Redirect();
            $redirect->fromArray(array(
              'old_url' => '/catalog/'.$record->token.'/',
              'new_url' => '/catalog/'.(!empty($record->token_prefix) ? ($record->token_prefix.'/'.$record->token) : $record->token).'/',
            ));
            if ($redirect->old_url == $redirect->new_url)
            {
              unset($redirect);
            }
          }

          $record->token = !empty($token) ? $token : null;
          $record->token_prefix = !empty($token_prefix) ? $token_prefix : null;

          try {
            $record->save();
            if (isset($redirect))
            {
              $redirect->save();
              unset($redirect);
            }
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
