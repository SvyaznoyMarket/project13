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
    //myDebug::dump($response, 1);
    if ($response['confirmed'])
    {
      $task = new Task();
      $task->setContentData(array(
        'type'      => 'init',
        'id'        => $response['id'],
        'packet_id' => $response['packet_id'],
        'sync_id'   => $response['sync_id'],
        'status'    => 'run',
        'prepared'  => array(), // массив моделей, таблицы которых подготовлены к загрузке данных
      ));

      $task->save();

      $this->getUser()->setFlash('message', 'Задача успешно запущена');
    }
    else {
      $this->getUser()->setFlash('error', "Не удалось запустить задачу. Ответ от core:\n".sfYaml::dump($response));
    }


    $this->redirect('homepage');
  }

  protected function getCore()
  {
    return Core::getInstance();
  }
}
