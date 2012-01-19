<?php

class myWebDebugPanelCore extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="/images/icons/lightbulb_gray.png" alt="Core logs" height="16" width="16" /> core';
  }

  public function getPanelTitle()
  {
    return 'Last core logs';
  }

  public function getPanelContent()
  {
    $content = '';

    $output = array();
    exec('tail -n 40 '.sfConfig::get('sf_log_dir').'/core_lib.log', $output);

    $content = implode("\n", $output);

    return '<pre>'.$content.'</pre>';
  }

  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('core', new self($event->getSubject()));
  }
}