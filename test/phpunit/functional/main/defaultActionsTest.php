<?php
require_once dirname(__FILE__).'/../../bootstrap/functional.php';

class functional_main_defaultActionsTest extends sfPHPUnitBaseFunctionalTestCase
{
  protected function getApplication()
  {
    return 'main';
  }

  public function testDefault()
  {
    $browser = $this->getBrowser();

    $browser->
      info('Homepage')->
      clearCookies()->
      setCookie(sfConfig::get('app_welcome_cookie_name'), md5(sfConfig::get('app_welcome_secret')))->
      get('/')->

      with('request')->begin()->
        isParameter('module', 'default')->
        isParameter('action', 'index')->
      end()->

      with('response')->begin()->
        isStatusCode(200)->
      end()
    ;

    $browser->info('Root categories');

    $list = ProductCategoryTable::getInstance()->getRootList();
    foreach ($list as $record)
    {
      $browser->
        with('response')->begin()->
          checkElement('.topmenu a:contains("'.$record['name'].'")', true)->
        end()
      ;
    }
  }
}