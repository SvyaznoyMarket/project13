<?php

class myWebDebugPanelRedis extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="/images/icons/redis.png" alt="Core logs" height="16" width="16" /> redis';
  }

  public function getPanelTitle()
  {
    return 'Redis';
  }

  public function getPanelContent()
  {
    $content = '';

    $output = array();
    exec('redis-cli INFO', $output);

    $content = implode("\n", $output);

    return '<pre>'.$content.'</pre>';
  }

  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('redis', new self($event->getSubject()));
  }
}