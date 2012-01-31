<?php
require_once dirname(__FILE__).'/../../bootstrap/functional.php';

class functional_main_productCatalogActionsTest extends sfPHPUnitBaseFunctionalTestCase
{
  protected function getApplication()
  {
    return 'main';
  }

  public function testRootCategory()
  {
    $browser = $this->getBrowser();
    $table = ProductCategoryTable::getInstance();

    $list = $table->getRootList();
    foreach ($list as $record)
    {
      $browser->
        clearCookies()->
        setCookie(sfConfig::get('app_welcome_cookie_name'), md5(sfConfig::get('app_welcome_secret')))->
        info("Catalog category #{$record['id']}")->
        get("/catalog/{$table->getRecordUrlToken($record)}/")->

        with('request')->begin()->
          isParameter('module', 'productCatalog')->
          isParameter('action', 'category')->
        end()->

        with('response')->begin()->
          isStatusCode(200)->
          checkElement('h1:contains("'.$record['name'].'")', true)->
        end()
      ;
    }
  }

  public function testChildCategory()
  {
    $browser = $this->getBrowser();
    $table = ProductCategoryTable::getInstance();

    $list = $table->getRootList();
    foreach ($list as $root)
    {
      foreach ($root->getChildList() as $record)
      {
        $browser->
          info("Catalog category #{$record['id']}")->
          clearCookies()->
          setCookie(sfConfig::get('app_welcome_cookie_name'), md5(sfConfig::get('app_welcome_secret')))->
          get("/catalog/{$table->getRecordUrlToken($record)}/")->

          with('request')->begin()->
            isParameter('module', 'productCatalog')->
            isParameter('action', 'category')->
          end()->

          with('response')->begin()->
            isStatusCode(200)->
            checkElement('h1:contains("'.$record['name'].'")', true)->
          end()
        ;
      }
    }
  }
}