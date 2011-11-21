<?php

class ProjectSeoImportTask extends sfBaseTask
{
    
  private $_CSVFileName = 'web/seo/seoData.csv';
  
  private $_CSVDelimeter = ";";
  
  private $_csvToDbRelation = array(
      'URL' => 'token',
      'title' => 'seo_title',
      'description' => 'seo_description',
      'keywords' => 'seo_keywords',
      'header' => 'seo_header',
      'seo-text' => 'seo_text'
 );
  
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
    $this->name             = 'SeoImport';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [Project:SeoImport|INFO] task does things.
Call it with:

  [php symfony Project:SeoImport|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $categoryList = file($this->_CSVFileName);
    $firstString = true;
    foreach($categoryList as $cat) {
        $categoryData = array();
        $data = explode($this->_CSVDelimeter, $cat);
        foreach($data as $numberKey => $string) {
            if ($firstString) {
                $string = trim($string);
                if (!isset($this->_csvToDbRelation[$string])) {
                    continue;
                }
                $numberKeyToField[$numberKey] = $this->_csvToDbRelation[$string];
                continue;
            }
            if (!isset($numberKeyToField[$numberKey])) {
                continue;
            }            
            $field = $numberKeyToField[$numberKey];
            $categoryData[$field] = $string;
            #echo $string . "\n";
        }
        $firstString = false;
        #print_r( $categoryData );
        if (!isset($categoryData['token'])) {
            continue;
        }        
        
        $q = Doctrine_Query::create()->update('ProductCategory');
        foreach($categoryData as $k => $f) {
            if ($k == 'token') continue;
            #echo $k .'----'.$f . "\n\n";
            $q->set($k, '?', $f);
        }
        $q->where('token = "' . $categoryData['token'] . '"'); 
        #echo $q->__toString() ."\n";
        $q->execute();                     
    }
    #print_r($numberKeyToField);

  }
}
