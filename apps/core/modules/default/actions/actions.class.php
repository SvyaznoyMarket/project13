<?php

/**
 * default actions.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends myActions
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
    if ($response['ready'] && !empty($response['packet_id']))
    {
      $task = new Task();
      $task->setContentData(array(
        'type'      => 'init',
        'packet_id' => $response['packet_id'],
        'sync_id'   => $response['sync_id'],
        'status'    => 'run',
      ));

      $task->save();
    }

    $this->redirect('homepage');
  }

  protected function getCore()
  {
    return Core::getInstance();
  }
}
