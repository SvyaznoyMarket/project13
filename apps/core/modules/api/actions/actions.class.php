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
    try {
      $response = trim(file_get_contents('php://input'));

      $logger = new sfFileLogger(new sfEventDispatcher(), array('file' => $this->getLogFilename()));
      $logger->log('Response: '.$response);

      $data = json_decode($response, true);

      $table = TaskTable::getInstance();

      if (!empty($data))
      {
        $taskType = 'project.'.$data['action'];

        $coreId = !empty($data['id']) ? $data['id'] : false;
        if ($coreId)
        {
          if ($task = $table->getByCoreId($coreId))
          {
            $task->setContentData($data);
            $task->core_packet_id = isset($data['packet_id']) ? $data['packet_id'] : null;
            $task->trySave();
          }
        }
        else if (!empty($data['action']))
        {
          //$packetId = !empty($data['packet_id']) ? $data['packet_id']: false;
          $packets = !empty($data['packet']) && is_array($data['packet']) ? $data['packet'] : false;
          //if ($packetId)
          if ($packets)
          {
            // находит максимальный номер пакета в бд
            $maxPacketId = $table->getMaxCorePacketId($taskType);

            // находит минимальный номер пакета в ответе core
            $minPacketId = isset($packets[0]['id']) ? $packets[0]['id'] : 0;

            // если обнаружены пробылы в очередности пакетов
            if (($minPacketId - $maxPacketId) > 1)
            {
              $maxPacketId++;
              foreach (range($minPacketId, $maxPacketId) as $v)
              {
                $logger->log("Packet's autogeneration from {$minPacketId} to {$maxPacketId}", sfLogger::WARNING);
                $packets[] = array(
                  'id'       => $v,
                  'priority' => 1, // общая очередь
                  'type'     => '[]',
                );
              }
            }

            foreach ($packets as $packet)
            {
              $task = new Task();
              $task->fromArray(array(
                'type'           => $taskType,
                'core_packet_id' => $packet['id'],
                'core_priority'  => $packet['priority'],
              ));
              $task->start_priority = $task->priority;
              $task->setContentData($data);

              if (!$task->trySave())
              {
                $logger->log("Can't create task #packet={$packet['id']}", sfLogger::CRIT);
              }
            }
          }
          else {
            $task = new Task();
            $task->fromArray(array(
              'type' => $taskType,
            ));
            $task->setContentData($data);
            if (!$task->trySave())
            {
              $logger->log("Can't create task #packet={$packet['id']}", sfLogger::CRIT);
            }
          }
        }
      }

      return $this->renderJson(array(
        'confirmed' => true,
      ));
    }
    catch (Exception $e) {
      $logger->err($e->getCode().' - '.$e->getMessage());
    }


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
