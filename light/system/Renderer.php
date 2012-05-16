<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/exception/routerException.php');
//require_once(ROOT_PATH.'lib/log4php/Logger.php');


class Renderer
{

  /**
   * @var string
   */
  private $templatePath;


  /**
   * @param string $filePath
   * @param array $data varName => varValue
   * @return string
   */

  public function __construct(){
    $this->templatePath = VIEW_PATH.'template/';
  }

  public function renderFile($filePath, $data=array()){
    Logger::getLogger('Renderer')->info('render file '.$filePath);
    $filePath = preg_replace('/^[\/\\\]*(.*?)(\.php)?$/i', '${1}', $filePath);
    $filePath = $this->templatePath.$filePath.'.php';

    if(!file_exists($filePath)){
      Logger::getLogger('Renderer')->error('template '.$filePath.' not found');
      throw new routerException('template '.$filePath.' not found');
    }

    extract($data, EXTR_REFS);
    ob_start();

    include($filePath);
    $return = ob_get_contents();
    ob_end_clean();

    return $return;
  }
}