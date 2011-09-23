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

    $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => sfConfig::get('sf_log_dir').'/api.log'));
    $logger->log($response);

    $data = json_encode($response);

    if (!empty($data))
    {
      $coreId = !empty($data['id']) ? $data['id'] : false;
      if ($coreId)
      {
        $task = TaskTable::getInstance()->createQuery()
          ->where('core_id = ?', $coreId)
          ->orderBy('updated_at DESC')
          ->fetchOne()
        ;
        if ($task)
        {
          $task->setContentData($response);
          $task->save();
        }
      }
    }

    return $this->renderJson(array(
      'confirmed' => true,
    ));

    return sfView::NONE;
  }
}
