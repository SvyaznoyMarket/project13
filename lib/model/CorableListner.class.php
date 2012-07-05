<?php

class Doctrine_Template_Listener_Corable extends Doctrine_Record_Listener
{
  protected $_options = array();

  public function __construct(array $options)
  {
    $this->_options = $options;
  }

  public function preInsert(Doctrine_Event $event)
  {
    if (isset($this->_options['check']) && method_exists($event->getInvoker(), $this->_options['check']))
    {
      $method = $this->_options['check'];
      if ($event->getInvoker()->$method())
      {
        return true;
      }
    }

    if (!$this->isPush($event->getInvoker()))
    {
      return true;
    }

    $method = 'create'.ucfirst($event->getInvoker()->getTable()->getComponentName());
    if ($response = Core::getInstance()->$method($event->getInvoker()))
    {
      $event->getInvoker()->core_id = $response;
    }
    else
    {
      throw new Exception("Unable to save to Core: " . current(Core::getInstance()->getError()));
    }

    return true;
  }

  public function preUpdate(Doctrine_Event $event)
  {
    if (isset($this->_options['check']) && method_exists($event->getInvoker(), $this->_options['check']))
    {
      $method = $this->_options['check'];
      if ($event->getInvoker()->$method())
      {
        return true;
      }
    }

    if (!$this->isPush($event->getInvoker()))
    {
      return true;
    }

    $method = 'update'.ucfirst($event->getInvoker()->getTable()->getComponentName());
    $response = Core::getInstance()->$method($event->getInvoker());

    if (!$response)
    {
      throw new Exception("Unable to save to Core: " . current(Core::getInstance()->getError()));
    }
  }

  public function preDelete(Doctrine_Event $event)
  {
    if (isset($this->_options['check']) && method_exists($event->getInvoker(), $this->_options['check']))
    {
      $method = $this->_options['check'];
      if ($event->getInvoker()->$method())
      {
        return true;
      }
    }

    if (!$this->isPush($event->getInvoker()))
    {
      return true;
    }
    $method = 'delete'.ucfirst($event->getInvoker()->getTable()->getComponentName());
    $response = Core::getInstance()->$method($event->getInvoker()->core_id);

    if (!$response)
    {
      throw new Exception("Unable to delete from Core: " . current(Core::getInstance()->getError()));
    }

  }

  protected function isPush(myDoctrineRecord $record)
  {
    return $record->getCorePush() && !(isset($this->_options['push']) && 'disable' == $this->_options['push']);
  }
}
