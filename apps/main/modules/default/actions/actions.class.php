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
    $promoData = array();
    foreach (RepositoryManager::getPromo()->get() as $i => $promo) {
      if (null == $url = $promo->getUrl()) continue;

      /* @var $promo PromoEntity */
      $promoItem = array(
        'alt'   => $promo->getName(),
        'imgs'  => $promo->getImageUrl(0),
        'imgb'  => $promo->getImageUrl($promo->isExclusive() ? 2 : 1),
        'url'   => $url,
        't'     =>
        !empty($banner['timeout'])
          ? $banner['timeout']
          : ($i > 0 ? sfConfig::get('app_banner_timeout', 6000) : 10000)
      ,
        'ga'    => $promo->getId().' - '.$promo->getName(),
      );

      if ($promo->isExclusive())
      {
        $promoItem['is_exclusive'] = true;
      }
      if (empty($promoItem['imgs']) || empty($promoItem['imgb'])) continue;

      $promoData[] = $promoItem;
    }

    $this->setVar('promoData', $promoData, true);
  }
 /**
  * Executes welcome action
  *
  * @param sfRequest $request A request object
  */
  public function executeWelcome(sfWebRequest $request)
  {
    sfConfig::set('sf_web_debug', false); // важно!

    if (!sfConfig::get('app_welcome_enabled', false))
    {
      //$this->redirect('@homepage', 301);
    }

    $this->setLayout('welcome');

    $cookieName = sfConfig::get('app_welcome_cookie_name');
    $secret = sfConfig::get('app_welcome_secret');
    if ($request->isMethod('post'))
    {
      if (
        ($secret == $request->getParameter($cookieName))
        || (sfConfig::get('sf_csrf_secret') == $request->getParameter($cookieName))
      ) {
        $this->getResponse()->setCookie($cookieName, md5($secret));
        $this->redirect($request['url'] ? $request['url'] : '@homepage');
      }
    }

    $this->url = $request['url'] ? $request['url'] : $request->getUri();
  }
 /**
  * Executes error404 action
  *
  * @param sfRequest $request A request object
  */
  public function executeError404(sfWebRequest $request)
  {
	  $this->setLayout(false);
  }
}
