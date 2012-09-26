<?php

namespace light;

require_once __DIR__ . '/../../light/system/Router.php';

class RouterTest extends \PHPUnit_Framework_TestCase
{
  public function testSetRouteRule()
  {
    $router = new Router;
    $routeRule = new RouteRule('foo/route', 'foo/pattern');
    $router->setRouteRuleList(array($routeRule));
    $this->assertEquals('foo/route', $router->matchUrl('foo/pattern'));
  }

  public function testUrlNotFound()
  {
    $this->setExpectedException('RuntimeException');
    $router = new Router;
    $router->matchUrl('404');
  }

  public function testRouteNotFound()
  {
    $this->setExpectedException('RuntimeException');
    $router = new Router;
    $router->createUrl('404');
  }

  public function testRouteNotFoundParam()
  {
    $router = Router::fromArray(array(
      array('foo/bar/<id:\d+>', 'foo/bar')
    ));
    $this->assertEquals('/foo/bar/123', $router->createUrl('foo/bar', array('id' => 123)));

    $this->setExpectedException('RuntimeException');
    $router->createUrl('foo/bar');
  }

  public function testSimpleRoute()
  {
    $router = Router::fromArray(array(
      array('foo/pattern', 'foo/route')
    ));
    $this->assertEquals('foo/route', $router->matchUrl('foo/pattern'));
    $this->assertEquals('foo/route', $router->matchUrl('/foo/pattern'));
    $this->assertEquals('/foo/pattern', $router->createUrl('foo/route'));
    $this->assertEquals('/foo/pattern', $router->createUrl('/foo/route'));
  }

  public function testCreateUrlWithHash()
  {
    $router = Router::fromArray(array(
      array('foo/pattern', 'foo/route')
    ));
    $this->assertEquals('/foo/pattern#ancor', $router->createUrl('foo/route', array('#' => 'ancor')));
  }

  public function testCreateUrlWithParams()
  {
    $router = Router::fromArray(array(
      array('foo/pattern', 'foo/route')
    ));
    $this->assertEquals('/foo/pattern?key=value', $router->createUrl('foo/route', array('key' => 'value')));
  }

  public function testLoadRequestAndGetArrays()
  {
    $this->assertArrayNotHasKey('c', $_GET);
    $this->assertArrayNotHasKey('c', $_REQUEST);
    $this->assertArrayNotHasKey('c', $_POST);

    $router = Router::fromArray(array(
      array('<c:(\w+)>/url', 'foo/route')
    ));

    $this->assertEquals('foo/route', $router->matchUrl('foo/url'));

    $this->assertArrayHasKey('c', $_GET);
    $this->assertArrayHasKey('c', $_REQUEST);
    $this->assertArrayNotHasKey('c', $_POST);
  }

  public function testExpressionRoute()
  {
    $router = Router::fromArray(array(
      array('<c:(\w+)>/url', '<c>/route')
    ));
    $this->assertEquals('foo/route', $router->matchUrl('foo/url'));
    $this->assertEquals('foo/route', $router->matchUrl('/foo/url'));
    $this->assertEquals('fooo/route', $router->matchUrl('fooo/url'));
    $this->assertEquals('fooo/route', $router->matchUrl('/fooo/url'));
    $this->assertEquals('/foo/url', $router->createUrl('foo/route'));
    $this->assertEquals('/foo/url', $router->createUrl('/foo/route'));
    $this->assertEquals('/fooo/url', $router->createUrl('fooo/route'));
    $this->assertEquals('/fooo/url', $router->createUrl('/fooo/route'));
  }

  public function testCreateUrlPatternNotFound()
  {
    $router = Router::fromArray(array(
      array('foo/<a>', 'foo/<a>')
    ));

    $this->assertEquals('/foo/bar', $router->createUrl('foo/bar'));
    $this->setExpectedException('RuntimeException');
    $router->createUrl('bar/bar');
  }

  public function testMatchUrlPatternNotFound()
  {
    $router = Router::fromArray(array(
      array('foo/<a>', 'foo/<a>')
    ));

    $this->assertEquals('foo/bar', $router->matchUrl('foo/bar'));
    $this->setExpectedException('RuntimeException');
    $router->matchUrl('bar/bar');
  }

  public function testAppendRoute()
  {
    $router = Router::fromArray(array(
      array('foo/*', 'foo/route')
    ));

    $this->assertEquals('foo/route', $router->matchUrl('foo/wefwefewfefw'));
  }

  public function testExpressionActionRoute()
  {
    $router = Router::fromArray(array(
      array('<c:(\w+)>/<a:\w+>', '<c>/<a>')
    ));
    $this->assertEquals('foo/bar', $router->matchUrl('foo/bar'));
    $this->assertEquals('controller/action', $router->matchUrl('controller/action'));
  }

  public function testLastSlashMatch()
  {
    $router = Router::fromArray(array(
      array('foo/bar/', 'foo/bar')
    ));
    $this->assertEquals('foo/bar', $router->matchUrl('foo/bar/'));
    $this->setExpectedException('RuntimeException');
    $router->matchUrl('foo/bar');
  }

  public function testLastSlasCreate()
  {
    $router = Router::fromArray(array(
      array('foo/bar/', 'foo/bar')
    ));
    $this->assertEquals('/foo/bar/', $router->createUrl('foo/bar'));
  }

  public function _testSpeed()
  {
    $start = microtime(1);
    $router = new Router();
    for ($i = 0; $i < 2500; $i++) {
      $router->addRouteRule(new RouteRule('<foo:\w+>', 'show/<foo>'));
      $router->addRouteRule(new RouteRule('<fooa:\w+>', 'show/<fooa>'));
      $router->addRouteRule(new RouteRule('<foos:\w+>', 'show/<foos>'));
      $router->addRouteRule(new RouteRule('<food:\w+>', 'show/<food>'));
      $router->addRouteRule(new RouteRule('<foof:\w+>', 'show/<foof>'));
      $router->addRouteRule(new RouteRule('<foog:\w+>', 'show/<foog>'));
      $router->addRouteRule(new RouteRule('<fooh:\w+>', 'show/<fooh>'));
      $router->addRouteRule(new RouteRule('<fooj:\w+>', 'show/<fooj>'));
      $router->addRouteRule(new RouteRule('<fook:\w+>', 'show/<fook>'));
      $router->addRouteRule(new RouteRule('<fool:\w+>', 'show/<fool>'));
    }
    $end = microtime(1) - $start;
    echo sprintf('Initialize  %d rules at %f sec, per 1 rule %0.20f sec', $i * 10, $end, $end / ($i * 10)), PHP_EOL;

    $start = microtime(1);
    $data = serialize($router);
    $end = microtime(1) - $start;
    echo sprintf('Serialize   %d rules at %f sec, per 1 rule %0.20f sec', $i * 10, $end, $end / ($i * 10)), PHP_EOL;

    $start = microtime(1);
    unserialize($data);
    $end = microtime(1) - $start;
    echo sprintf('Unserialize %d rules at %f sec, per 1 rule %0.20f sec', $i * 10, $end, $end / ($i * 10)), PHP_EOL;


    $router = Router::fromArray(array(
      array('<c:\w+>/url', '<c>/route')
    ));

    $start = microtime(1);
    for ($i = 0; $i < 6000; $i++) {
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
      $router->createUrl('ewf/route');
    }
    $end = microtime(1) - $start;
    echo sprintf('Create      %d urls  at %f sec, per 1 call %0.20f sec', $i * 10, $end, $end / ($i * 10)), PHP_EOL;

    $start = microtime(1);
    for ($i = 0; $i < 6000; $i++) {
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
      $router->matchUrl('wef/url');
    }
    $end = microtime(1) - $start;
    echo sprintf('Found       %d urls  at %f sec, per 1 call %0.20f sec', $i * 10, $end, $end / ($i * 10)), PHP_EOL;
  }
}
