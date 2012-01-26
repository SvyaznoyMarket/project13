<?php

class AssetCombineTask extends sfBaseTask
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
      new sfCommandOption('force', null, sfCommandOption::PARAMETER_NONE, 'Force minimeze'),
      // add your own options here
    ));

    $this->namespace        = 'asset';
    $this->name             = 'combine';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [AssetCombine|INFO] task does things.
Call it with:

  [php symfony AssetCombine|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $configuration = ProjectConfiguration::getApplicationConfiguration($options['application'], $options['env'], true);

    // add your code here
    $combineFilename = sfConfig::get('app_js_combine_file');
    if (!file_exists($combineFilename))
    {
      $this->logBlock("File $combineFilename doesn't exist", 'ERROR');
      exit();
    }

    $data = json_decode(file_get_contents($combineFilename), true);
    foreach ($data as $filename => &$timestamp)
    {
      $file = sfConfig::get('sf_web_dir').$filename;
      $tmp = filemtime($file);

      if ($options['force'] || ($tmp != $timestamp))
      {
        $this->log($file);
        $timestamp = $tmp;

        $targetFile = preg_replace('/.js$/', '.min.js', $file);

        $command = 'java -jar '.sfConfig::get('app_js_combine_compiler').' --charset utf-8 --js '.$file.' --js_output_file '.$targetFile;
        //myDebug::dump($command);
        exec($command, $output, $return);
        if (0 !== $return)
        {
          $this->logBlock('Error compile '.$file, 'ERROR');
          exec('cp '.$file.' '.$targetFile);
        }
      }

    } if (isset($timestamp)) unset($timestamp);

    file_put_contents($combineFilename, json_encode($data));
  }
}
