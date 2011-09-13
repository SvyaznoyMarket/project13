<?php

/**
 * api actions.
 *
 * @package    enter
 * @subpackage api
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/core.log'));
    $logger->log(sfYaml::dump($_REQUEST));

    return sfView::NONE;
  }
}
