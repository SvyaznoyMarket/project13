<?php

class myViewCacheManager extends sfViewCacheManager
{
  public function getPartialCache($module, $action, $cacheKey)
  {
    $uri = $this->getPartialUri($module, $action, $cacheKey);

    if (!$this->isCacheable($uri))
    {
      return null;
    }

    // retrieve content from cache
    $cache = $this->get($uri);

    if (null === $cache)
    {
      return null;
    }

    $cache = is_string($cache) ? unserialize($cache) : $cache;
    $content = $cache['content'];
    $this->context->getResponse()->merge($cache['response']);

    if (sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => false)), $content)->getReturnValue();
    }

    return $content;
  }

  public function getPageCache($uri)
  {
    $retval = $this->get($uri);

    if (null === $retval)
    {
      return false;
    }

    $cachedResponse = is_string($retval) ? unserialize($retval) : $retval;
    $cachedResponse->setEventDispatcher($this->dispatcher);

    if (sfView::RENDER_VAR == $this->controller->getRenderMode())
    {
      $this->controller->getActionStack()->getLastEntry()->setPresentation($cachedResponse->getContent());
      $this->context->getResponse()->setContent('');
    }
    else
    {
      $this->context->setResponse($cachedResponse);

      if (sfConfig::get('sf_web_debug'))
      {
        $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => false)), $this->context->getResponse()->getContent())->getReturnValue();

        $this->context->getResponse()->setContent($content);
      }
    }

    return true;
  }
}