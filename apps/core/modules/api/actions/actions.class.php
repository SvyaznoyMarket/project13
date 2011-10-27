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
    $response = trim(file_get_contents('php://input'));

    $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getLogFilename()));
    $logger->log('Response: '.$response);

    $data = json_decode($response, true);

    if (!empty($data))
    {
      $coreId = !empty($data['id']) ? $data['id'] : false;
      if ($coreId)
      {
        if ($task = TaskTable::getInstance()->getByCoreId($coreId))
        {
          $task->setContentData($data);
          $task->save();
        }
      }
      else if (!empty($data['action']))
      {
        $task = new Task();
        $task->fromArray(array(
          'type'   => 'project.'.$data['action'],
        ));
        $task->setContentData($data);
        $task->save();
      }
    }

    return $this->renderJson(array(
      'confirmed' => true,
    ));

    return sfView::NONE;
  }

 /**
  * Executes log action
  *
  * @param sfRequest $request A request object
  */
  public function executeLog(sfWebRequest $request)
  {
    $count = $request->getParameter('count', 40);
    $file = $this->getLogFilename();

    $this->content = is_readable($file) ? shell_exec("tail -n {$count} {$file}") : false;
   }



   protected function getLogFilename()
   {
     return sfConfig::get('sf_log_dir').'/api.log';
   }
}
