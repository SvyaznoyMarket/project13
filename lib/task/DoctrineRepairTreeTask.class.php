<?php

class DoctrineRepairTreeTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model name'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'main_dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
      new sfCommandOption('check', null, sfCommandOption::PARAMETER_NONE, 'Only check the tree'),
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'repair-tree';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [doctrine:repair-tree|INFO] task repairs nested set tree.
Call it with:
  php symfony doctrine:dump-tree ProductCategory [options]

  options:
    check - Checks is a tree vaild

  [php symfony doctrine:repair-tree|INFO]
EOF;
    $this->logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/repair-tree.log'));
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $modelName = $arguments['model'];
    $table = Doctrine_Core::getTable($modelName);

    $this->logSection('INFO', 'Start '.($options['check'] ? 'checking' : 'repairing').' '.$modelName.'\'s tree');

    $this->updateChildren($table);

    $this->logSection('INFO', 'Done '.($options['check'] ? 'checking' : 'repairing').' '.$modelName.'\'s tree');

  }

  protected function updateChildren($table, $parent = null, $level = 0, $i = 0)
  {
    $q = $table->createQuery();
    if (!empty($parent))
    {
      $q->where('core_parent_id = ?', $parent->core_id);
    }
    else
    {
      $q->where('core_parent_id is NULL');
    }
    
    if ($table->hasColumn('position')) {
        $q->orderBy('position ASC');        
    } elseif ($table->hasColumn('core_lft')) {
        $q->orderBy('core_lft DESC');
    } else {
        $q->orderBy('id DESC');        
    }

    $list = $q->execute();

    $tree = array(
      'lft' => 0,
      'rgt' => 0,
      'level' => 0,
    );

    foreach ($list as $item)
    {
      if (0 == $level) $i = 0;

      $tree['level'] = $level;
      $i++;
      $tree['lft'] = $i;
      $i = $this->updateChildren($table, $item, $level + 1, $i) + 1;
      $tree['rgt'] = $i;

      if ($item['level'] != $tree['level'] || $item['lft'] != $tree['lft'] || $item['rgt'] != $tree['rgt'])
      {
        $this->logSection('ERROR', 'broken entity #'.$item['id'].' '.$item['name']);
        if (!$options['check'])
        {
          $item->level = $tree['level'];
          $item->lft = $tree['lft'];
          $item->rgt = $tree['rgt'];
          $item->save();
        }
      }
    }

    return $i;
  }
}
