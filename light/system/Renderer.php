<?php
namespace light;
use InvalidArgumentException;
use Logger;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/log4php/Logger.php');

class Renderer
{

  /**
   * @var string
   */
  protected  $templatePath;

  /**
   * @static
   * @return Renderer
   */
  public static function getInstance(){
    static $instance;
    if (!$instance) {
      $instance = new Renderer();
    }
    return $instance;
  }

  protected function __construct(){
    $this->templatePath = Config::get('viewPath').'template/';
  }

 /**
  * @param string $filePath
  * @param array $data
  * @return string
  * @throws routerException
  */
  public function renderFile($filePath, $data=array()){
    Logger::getLogger('Renderer')->info('render file '.$filePath);
    $filePath = preg_replace('/^[\/\\\]*(.*?)(\.php)?$/i', '${1}', $filePath);
    $filePath = $this->templatePath.$filePath.'.php';

    if(!file_exists($filePath)){
      Logger::getLogger('Renderer')->error('template '.$filePath.' not found');
      throw new \RuntimeException('template '.$filePath.' not found');
    }

    extract($data, EXTR_REFS);
    ob_start();

    include($filePath);
    $return = ob_get_contents();
    ob_end_clean();

    return $return;
  }
}


class HtmlRenderer extends Renderer{

    /**
     * @var array
     */
    private $js = array();

  /**
   * @var array
   */
  private $css = array();

  /**
   * @var string
   */
  private $description;

  /**
   * @var string
   */
  private $title;

  private $layoutPath = 'layout/';
  private $layout = 'layout';

  private $parameterList = array();

  private $page;

  /**
   * @static
   * @return Renderer
   */
  public static function getInstance(){
    static $instance;
    if (!$instance) {
      $instance = new HtmlRenderer();
    }
    return $instance;
  }

  /**
   * @param string $path
   * @throws dataFormatException
   * @throws systemException
   *
   */
  public function addCss($path){
    if(!is_string($path)){
      throw new InvalidArgumentException('css path must be a string, in real its '. gettype($path));
    }
    foreach($this->css as $css){
      if($css == $path){
        throw new \RuntimeException('Css '. $path.' already exists');
      }
    }
    $this->css[] = $path;
  }

  /**
   * echo all css paths
   */
  public function showCss(){
    foreach($this->css as $css){
      echo '<link rel="stylesheet" type="text/css" media="screen" href="/css/'.$css.'" />'."\r\n";
    }
  }

    public function addJS($filePath)
    {
        if(!in_array($filePath, $this->js))
        {
            $this->js[] = $filePath;
        }
    }

    public function showJS()
    {
        foreach($this->js as $js)
        {
            echo '<script src="/js/'.$js.'"></script>';
        }
    }

  /**
   * @param string $title
   */
  public function setTitle($title){
    if(!is_string($title)){
      throw new InvalidArgumentException('page title must be a string, in real its '. gettype($title));
    }
    $this->title = $title;
  }

  /**
   * @return string
   */
  public function getTitle(){
    return !is_null($this->title) ? htmlspecialchars($this->title) : Config::get('defaultPagetTitle');
  }

  /**
   * @param string $title
   */
  public function setDescription($description){
    if(!is_string($description)){
      throw new InvalidArgumentException('page description must be a string, in real its '. gettype($description));
    }
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription(){
    return !is_null($this->description) ? htmlspecialchars($this->description) : Config::get('defaultPageDescription');
  }

  /**
   * @param string $route
   * @return string
   */

  public function url($route, $params=array()){
    try{
      if(!is_string($route)){
        throw new InvalidArgumentException('route for function url() must be string, in real: '.gettype($route));
      }
      if(!is_array($params)){
        throw new InvalidArgumentException('params for function url() must be an array, in real: '.gettype($params));
      }
      $link =  App::getRouter()->createUrl($route, $params);
      return $link;
    }
    catch(\Exception $e){
      Logger::getLogger('Renderer')->error($e->getMessage());
      return '#';
    }
  }

  public function addParameter($parameterName, $parameterValue)
  {
      $this->parameterList[$parameterName] = $parameterValue;
  }

  public function setPage($page)
  {
      $this->page = $page;
  }

  public function setLayout($layout)
  {
      $this->layout = $layout;
  }

    /**
     * Owned.
     */
    public function renderFile($filePath, $data=array())
  {
      $filePathPartList = explode('/', $filePath);
      $fillerName = $filePathPartList[count($filePathPartList) - 1];

      $filler = App::getFiller($fillerName);
      $filler?$filler->run():Null;

      return parent::renderFile($filePath, array_merge($data, $this->parameterList));
  }

  public function render()
  {
      $this->parameterList['page'] = $this->page;

      return $this->renderFile($this->layoutPath . $this->layout, $this->parameterList);
  }

}