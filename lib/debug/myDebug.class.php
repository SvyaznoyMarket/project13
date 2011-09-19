<?php

/**
 * swDebug.
 *
 * @author     Lazukin Georgiy ~green
 */
class myDebug
{
  static private $count = 0;

  static public function dump($value, $terminate = false, $format = null)
  {
    self::$count ++;

    $type = gettype($value);
    if (is_object($value))
    {
      $type .= ' '.get_class($value);
    }

    $value = self::varDump($value);

    echo strtr(
      (self::isCli() || sfContext::getInstance()->getRequest()->isXmlHttpRequest())
      ? "%%notice%% %%type%% %%length%%\n%%value%%\n\n"
      : '<pre title="'.(self::$count.'-й вызов').'" style="z-index: 900; position: absolute; margin-left: '.((self::$count - 1) * 24).'px; opacity: 0.70; -moz-box-shadow: 0 0 1em black; border: solid 1px #000; -moz-border-radius: 10px; font: normal 14px Courier New; background: #333; color: #00cc00; width: 18px; height: 18px; overflow: hidden;"><span onclick="var style = this.parentNode.style; if (style.width != \'18px\') { style[\'z-index\'] = \'900\'; style.width=\'18px\'; style.height=\'18px\'; style.overflow =\'hidden\'; style.opacity = \'0.70\'; } else { style[\'z-index\'] = \'999\'; style.width=\'1100px\'; style.height=\'400px\'; style.overflow =\'auto\'; style.opacity = \'1\'; }" oncontextmenu="this.parentNode.parentNode.removeChild(this.parentNode); return false;" style="float: left; background: #ff0000; color: #fff; font-weight: bold; font-family: Calibri, Arial; cursor: pointer; -moz-border-radius: 7px; -moz-border-radius: 7px;"> %%notice%% </span><span style="color: #ccc;"> %%type%% %%length%%</span><br />%%value%%</pre>'
      , array(
        '%%notice%%' => (self::isCli() && self::isWindows()) ? '!' : '♫',
        '%%type%%'   => $type,
        '%%value%%'  => self::varFormat($value, $format),
        '%%length%%' => is_bool($value) ? '' : (' ('.sizeof($value).')'),
      )
    );

    if ($terminate) exit();
  }

  protected static function varDump($value)
  {
    $return = $value;

    if ($value instanceof Doctrine_Collection or $value instanceof Doctrine_Record)
    {
      $return = $value->toArray();
    }
    else if ($value instanceof Doctrine_Query)
    {
      $return = $value->getSqlQuery();
    }
    else if ($value instanceof sfRequest)
    {
      $return = $value->getParameterHolder()->getAll();
    }
    else if ($value instanceof myUser)
    {
      $return = $value->getAttributeHolder()->getAll();
    }
    else if ($value instanceof Exception)
    {
      $return = $value->getMessage();
    }
    else if ($value instanceof sfDate)
    {
      $return = $value->format('Y-m-d H:i:s');
    }
    else if (is_object($value))
    {
      $return = array(
        'class'   => get_class($value),
        'methods' => get_class_methods($value),
        'vars'    => get_class_vars(get_class($value)),
      );
    }

    if (is_array($return))
    {
      foreach ($return as $i => $item)
      {
        $return[$i] = self::varDump($item);
      }
    }

    return $return;
  }

  protected static function varFormat($value, $format)
  {
    $return = '';

    if (null == $format)
    {
      $return = print_r($value, true);
    }
    else if (in_array($format, array('yml', 'yaml')))
    {
      $return = sfYaml::dump($value, 100);
    }

    return $return;
  }

  protected static function isCli()
  {
    return ('cli' == php_sapi_name()) && empty($_SERVER['REMOTE_ADDR']);
  }

  protected static function isWindows()
  {
    return 0 === strpos(PHP_OS, 'WIN');
  }
}