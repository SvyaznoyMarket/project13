<?php

class myDoctrineRecordListener extends Doctrine_Record_Listener
{
  public function postSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();

    $driver = $invoker->getTable()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
    $driver->deleteByPattern(
      sfConfig::get('app_doctrine_result_cache_prefix', ':dql')
      .$invoker->getTable()->getQueryRootAlias().'-'.$invoker->id
      .'*'
    );
  }

  public function postHydrate(Doctrine_Event $event)
  {
  }
}