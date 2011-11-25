<?php

class WelcomeFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    if (false
      || ('live' == sfConfig::get('sf_environment'))
      || ('prod' == sfConfig::get('sf_environment'))
      || in_array(sfConfig::get('sf_environment'), $this->getParameter('excluded_env'))
    ) {
      // ignore
    }
    else {
      $context = $this->getContext();
      if ($this->isFirstCall() && ('welcome' != $context->getRouting()->getCurrentRouteName()))
      {
        $request = $context->getRequest();
        $cookieName = sfConfig::get('app_welcome_cookie_name');
        $secret = sfConfig::get('app_welcome_secret');

        if ($request->getCookie($cookieName, false) !== md5($secret))
        {
          $context->getController()->forward('default', 'welcome');

          //$context->getController()->redirect('@welcome', 0);
          throw new sfStopException();
        }
      }
    }

    $filterChain->execute();
  }
}