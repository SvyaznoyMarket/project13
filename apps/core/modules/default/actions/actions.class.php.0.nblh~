<?php

/**
 * default actions.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
 /**
  * Executes init action
  *
  * @param sfRequest $request A request object
  */
  public function executeInit(sfWebRequest $request)
  {
    $response = $this->getCore()->query('load.start');
    myDebug::dump($response, 1);
  }

  protected function getCore()
  {
    return Core::getInstance();
  }
}
