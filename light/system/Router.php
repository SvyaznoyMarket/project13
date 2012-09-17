<?php
namespace light;

class Router
{
  /**
   * @var RouteRule[]
   */
  private $ruleList = array();

  /**
   * @static
   * @param array $config
   * <code>
   *  array(
   *      array('<controler:(foo|bar)>', '<controller>/index'),
   *      ...
   *  )
   * </code>
   * @return Router
   */
  public static function fromArray(array $config)
  {
    $router = new self;
    foreach ($config as $routeParams) {
      $router->addRouteRule(new RouteRule($routeParams[1], $routeParams[0]));
    }
    return $router;
  }

  /**
   * @param RouteRule[] $routeRuleList
   */
  public function setRouteRuleList(array $routeRuleList)
  {
    $this->ruleList = array();
    foreach ($routeRuleList as $routeRule)
      $this->addRouteRule($routeRule);
  }

  /**
   * @param RouteRule $rule
   */
  public function addRouteRule(RouteRule $rule)
  {
    $this->ruleList[] = $rule;
  }

  /**
   * Parses a URL based on this rule.
   * @param string $path
   * @return null|string the route that consists of the controller ID and action ID or null
   * @throws RuntimeException on error found route rule
   */
  public function matchUrl($path)
  {
    $path = '/' . trim($path, '/'); // add root path delimiter
    foreach ($this->ruleList as $routeRule) {
      if ($route = $routeRule->matchUrl($path))
        return $route;
    }
    throw new \RuntimeException(
      sprintf('Unable to resolve the request "%s"', $path),
      404
    );
  }

  /**
   * Constructs a URL.
   * @param string $route the controller and the action (e.g. article/read)
   * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
   * If the name is '#', the corresponding value will be treated as an anchor
   * and will be appended at the end of the URL.
   * @return string the constructed URL
   * @throws RuntimeException on error found route rule
   */
  public function createUrl($route, array $params = array())
  {
    if (isset($params['#'])) {
      $anchor = '#' . $params['#'];
      unset($params['#']);
    }
    else
      $anchor = '';
    $route = trim($route, '/');
    foreach ($this->ruleList as $routeRule) {
      if ($url = $routeRule->createUrl($route, $params))
        return $url . $anchor;
    }
    throw new \RuntimeException(sprintf('Unable to resolve the route "%s"', $route), 404);
  }
}

class RouteRule
{
  /**
   * @var string the controller/action pair
   */
  private $route;
  /**
   * @var string regular expression used to parse a URL
   */
  private $pattern;
  /**
   * @var array list of parameters (name=>regular expression)
   */
  private $routeParams = array();
  /**
   * @var array the mapping from route param name to token name (e.g. _r1=><1>)
   */
  private $urlParams = array();
  /**
   * @var boolean whether the URL allows additional parameters at the end of the path info.
   */
  private $append;
  /**
   * @var string the pattern used to match route
   */
  private $routePattern;
  /**
   * @var string template used to construct a URL
   */
  private $template;

  /**
   * Constructor
   * @param string $route
   * @param string $pattern
   */
  public function __construct($route, $pattern)
  {
    $this->route = trim($route, '/');
    $routePatternReplaces['/'] = $patternReplaces['/'] = '\\/';
    $templateReplaces = array();
    if (strpos($route, '<') !== false && preg_match_all('/<(\w+)>/', $route, $routeMatches)) {
      foreach ($routeMatches[1] as $name)
        $this->routeParams[$name] = '<' . $name . '>';
    }
    if (preg_match_all('/<(\w+):?(.*?)?>/', $pattern, $patternMatches)) {
      $tokens = array_combine($patternMatches[1], $patternMatches[2]);
      foreach ($tokens as $name => $value) {
        $iname = '<' . $name . '>';
        if ($value === '') {
          $value = '[^\/]+';
        }
        else {
          $templateReplaces['<' . $name . ':' . $value . '>'] = $iname;
        }
        $patternReplaces[$iname] = '(?' . $iname . $value . ')';
        if (isset($this->routeParams[$name]))
          $routePatternReplaces[$iname] = $patternReplaces[$iname];
        else
          $this->urlParams[$name] = $value;
      }
    }
    // pattern optimization
    $p = rtrim($pattern, '*');
    $this->append = $p !== $pattern;
    $p = '/' . trim($p, '/'); // add root path delimiter
    // render template for generate url
    if ($templateReplaces)
      $this->template = strtr($p, $templateReplaces);
    else
      $this->template = $p;
    // render pattern for match url
    $this->pattern = '/^' . strtr($this->template, $patternReplaces);
    if ($this->append)
      $this->pattern .= '/u';
    else
      $this->pattern .= '$/u';
    if ($this->routeParams !== array())
      $this->routePattern = '/^' . strtr($this->route, $routePatternReplaces) . '$/u';
  }

  /**
   * Parses a URL based on this rule.
   * @param string $path
   * @return null|string the route that consists of the controller ID and action ID or null
   */
  public function matchUrl($path)
  {
    if (preg_match($this->pattern, $path, $matches)) {
      $tr = array();
      foreach ($matches as $key => $value) {
        if (isset($this->routeParams[$key]))
          $tr[$this->routeParams[$key]] = $value;
        else if (isset($this->urlParams[$key]))
          $_REQUEST[$key] = $_GET[$key] = $value;
      }

      if ($this->routePattern !== null)
        return strtr($this->route, $tr);
      else
        return $this->route;
    }
    else
      return null;
  }

  /**
   * Creates a URL based on this rule.
   * @param string $route Route
   * @param array $params list of parameters
   * @return bool|string the constructed URL or false on error
   */
  public function createUrl($route, array $params = array())
  {
    $tr = array();
    if ($route !== $this->route) {
      if ($this->routePattern !== null && preg_match($this->routePattern, $route, $matches)) {
        foreach ($this->routeParams as $key => $name)
          $tr[$name] = $matches[$key];
      }
      else
        return false;
    }
    foreach ($this->urlParams as $key => $value)
    {
        $rawKey = '<' . $key . '>';
        $value = Null;
        if(isset($params[$key]))
        {
            $value = $params[$key];
            unset($params[$key]);
        }
        else
        {
            $rawKey = '/' . $rawKey;
        }

        $tr[$rawKey] = urlencode($value);
    }

    $url = strtr($this->template, $tr);
    if (empty($params))
      return $url;
    return $url . '?' . http_build_query($params);
  }
}