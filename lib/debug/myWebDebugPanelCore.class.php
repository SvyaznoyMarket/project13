<?php

class myWebDebugPanelCore extends sfWebDebugPanel
{
  protected
    $title = '',
    $content = ''
  ;

  public function __construct(sfWebDebug $webDebug)
  {
    parent::__construct($webDebug);

    $this->content = '';

    $output = array();
    exec('tail -n 40 '.sfConfig::get('sf_log_dir').'/core_lib.log', $output);

    $this->content = implode("\n", $output);
    $this->content = preg_replace_callback(
      '/\\\u([0-9a-fA-F]{4})/',
      create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
      $this->content
    );

    //$this->setStatus(sfLogger::WARNING);
    $this->title = '<img src="/images/icons/'.(false === strpos($this->content, '"error"') ? 'lightbulb_gray.png' : 'lightbulb.png').'" alt="Core logs" height="16" width="16" /> core';

    $this->content = '<pre>'.$this->content.'</pre>';
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function getPanelTitle()
  {
    return 'Last core logs';
  }

  public function getPanelContent()
  {
    return $this->content;
  }

  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('core', new self($event->getSubject()));
  }
}