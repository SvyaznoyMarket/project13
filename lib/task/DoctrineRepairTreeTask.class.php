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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('log', null, sfCommandOption::PARAMETER_NONE, 'Enable logging'),
      // add your own options here
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'repair-tree';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [DoctrineRepairTree|INFO] task does things.
Call it with:

  [php symfony DoctrineRepairTree|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    sfConfig::set('sf_logging_enabled', $options['log']);
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $modelName = $arguments['model'];
    $table = Doctrine_Core::getTable($modelName);
    
    $getRecordData = function (myDoctrineRecord $record)
    {
      $return = array();

      $except = array(
        'root_id',
        'lft',
        'rgt',
        'level',
      );

      foreach ($record->getTable()->getColumns() as $field => $v)
      {
        if (in_array($field, $except)) continue;

        $return[$field] = $record->get($field);
      }

      $return['children'] = array();

      return $return;
    };
    
    function getChildren($parent, $table, $getRecordData)
    {
      $data = array();
      
      $childList = $table->createQuery()
        ->where('core_parent_id = ?', $parent['core_id'])
        ->orderBy('position ASC')
        ->execute()
      ;
      foreach ($childList as $child)
      {
        $data["r_$child->id"] = $getRecordData($child);
        $data["r_$child->id"]['children'] = getChildren($child, $table, $getRecordData);
      }
      
      return count($data) ? $data : null;
    };
    
    // check for necessary columns
    foreach (array('id', 'core_id', 'core_parent_id', 'position') as $field)
    {
      if (!$table->hasColumn($field))
      {
        $this->logSection($modelName, "hasn't column {$field}", null, 'ERROR');
        
        return false;
      }
    }
    
    // clear nested set data
    $table->createQuery()
      ->update($modelName)
      ->set(array(
        'root_id' => null,
        'level'   => null,
        'lft'     => null,
        'rgt'     => null,
      ))
      ->execute()
    ;
 
    // get roots by core_parent_id
    $rootList = $table->createQuery()
      ->where('core_parent_id IS NULL')
      ->orderBy('position ASC')
      ->execute()
    ;

    $data = array();
    
    foreach ($rootList as $root)
    {
      //$tree->createRoot($root);
      $data["r_$root->id"] = $getRecordData($root);
      $data["r_$root->id"]['children'] = getChildren($root, $table, $getRecordData);
    }
    
    $data = array($modelName => $data);
    
    $content = sfYaml::dump($data, 100);
    file_put_contents(sfInflector::underscore($modelName).'.yml', $content);
  }

}
