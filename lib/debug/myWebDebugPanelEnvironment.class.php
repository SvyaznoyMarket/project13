<?php

class myWebDebugPanelEnvironment extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="/images/icons/'.('debug' == sfConfig::get('sf_environment') ? 'world.png' : 'world_gray.png').'" alt="Environment switcher" height="16" width="16" /> env';
  }

  public function getTitleUrl()
  {
    $request = sfContext::getInstance()->getRequest();
    $app = sfConfig::get('sf_app');
    $env = sfConfig::get('sf_environment');

    $uri = $request->getUri();

    $uri = str_replace(
      "/{$app}_{$env}.php",
      'debug' != $env ? "/{$app}_debug.php" : "/{$app}_dev.php",
      $uri
    );

    return $uri;
  }

  public function getPanelTitle()
  {
    return 'Environment switcher';
  }

  public function getPanelContent()
  {
    return;
  }

  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('environment', new self($event->getSubject()));
  }
}