<?php

class dmDebug
{

  const MAX_DEBUG_LENGTH = 1000000;

  public function __construct()
  {
    self::kill(func_get_args());
  }

  /**
   * Gets a sfTimer instance.
   *
   * It returns the timer named $name or create a new one if it does not exist.
   *
   * @param string $name The name of the timer
   *
   * @return sfTimer The timer instance
   */
  public static function timer($name)
  {
    return sfTimerManager::getTimer('[Diem] '.$name);
  }
  
  /**
   * @return sfTimer if logging is enabled or null if logging is disabled
   */
  public static function timerOrNull($name)
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      return self::timer($name);
    }
    
    return null;
  }

  /**
   * How many time elapsed since request ?
   */
  public static function getTimeFromStart()
  {
    return sprintf('%.0f', (microtime(true) - dm::getStartTime()) * 1000);
  }

  /**
   * Builds an string
   *
   * @param $something Any PHP type
   *
   * @return string An formatted string
   */
  protected static function formatAsString($something)
  {
    if (is_array($something))
    {
      foreach($something as $key => $val)
      {
        if (!is_string($val))
        {
          $something[$key] = print_r($val, true);
        }
      }
      $string = implode(" - ", $something);
    }
    else
    {
      $string = (string) $string;
    }

    $return = substr($string, 0, self::MAX_DEBUG_LENGTH);

    if (strlen($string) > self::MAX_DEBUG_LENGTH)
    {
      $return .= "\n---TRUNCATED---\n";
    }

    return htmlspecialchars($return);
  }


  /**
   * Logs all parameters with symfony logger
   */
  public static function log()
  {
    dmContext::getInstance()->getLogger()->err(self::formatAsString(func_get_args()));
  }

  /**
   * Shows all parameter in the page with a <div>
   */
  public static function show()
  {
    return self::debugger(func_get_args(), 2, array('tag' => 'div'));
  }
  
  /**
   * Shows all parameter in the page with a <pre>
   */
  public static function showPre()
  {
    return self::debugger(func_get_args(), 2, array("tag" => "pre"));
  }

  /**
   * Shows all parameter in the page with a <div>
   */
  public static function traceShow()
  {
    return self::debugger(func_get_args(), 2, array("tag" => "div"));
  }

  /**
   * Shows all parameter in the page with a <pre>, even if debugging is disabled
   */
  public static function traceForce()
  {
    return self::debugger(func_get_args(), 2, array("force" => true));
  }

  public static function traceString()
  {
    return self::debugger(func_get_args(), 2, array("to_string" => true));
  }

  public static function kill()
  {
    return self::debugger(func_get_args(), 3);
  }

  public static function killString()
  {
    return self::debugger(func_get_args(), 3, array("to_string" => true));
  }

  public static function killForce()
  {
    return self::debugger(func_get_args(), 3, array("force" => true));
  }
  
  public static function showForce()
  {
    return self::debugger(func_get_args(), 2, array("force" => true, 'tag' => 'pre'));
  }

  public static function simpleStack($msg = "")
  {
    $result = "$msg\n";
    $trace = debug_backtrace();
    foreach ($trace as $element)
    {
      if ($first)
      {
        $first = false;
      }
      else
      {
        $result .= "File: " . $lastFile . " function: " . (isset($element['function']) ? $element['function'] : '') . " line: " .$lastLine . "\n<br />";
      }
      $lastFile = isset($element['file']) ? $element['file'] : '';
      $lastLine = isset($element['line']) ? $element['line'] : '';
    }
    echo $result;
  }

  /**
   * Displays a debug backtrace improved with javascript
   */
  public static function stack($msg = "")
  {
    if (!dmContext::hasInstance())
    {
      return self::simpleStack($msg);
    }

    dmContext::getInstance()->getConfiguration()->loadHelpers(array('Javascript', 'Tag'));

    $result = "";
    $trace = debug_backtrace();
    $traceId = "pkSimpleBacktrace" . dmString::random();
    $traceIdShow = $traceId . "Show";
    $traceIdHide = $traceId . "Hide";
    $result .= "<div class='pkSimpleBacktrace'>stack $msg" .
    link_to_function("&gt;&gt;&gt;",
        "document.getElementById('$traceId').style.display = 'block'; " .
        "document.getElementById('$traceId').style.display = 'block'; " .
        "document.getElementById('$traceIdShow').style.display = 'none'; " .
        "document.getElementById('$traceIdHide').style.display = 'inline'",
    array("id" => $traceIdShow)) .
    link_to_function("&lt;&lt;&lt;",
        "document.getElementById('$traceId').style.display = 'none'; " .
        "document.getElementById('$traceIdHide').style.display = 'none'; " .
        "document.getElementById('$traceIdShow').style.display = 'inline'",
    array("id" => $traceIdHide, "style" => 'display: none'));
    $result .= "</div>";
    $result .= "<pre id='$traceId' style='display: none'>\n";
    $first = true;
    foreach ($trace as $element)
    {
      if ($first)
      {
        $first = false;
      }
      else
      {
        $result .= "File: " . $lastFile . " function: " . $element['function'] . " line: " .$lastLine . "\n";
      }
      $lastFile = dmArray::get($element, 'file');
      $lastLine = dmArray::get($element, 'line');
    }
    $result .= "</pre>\n";
    echo $result;
  }

  protected static function debugger($var, $level = 1, $opt = array())
  {
    $CR = "\n";

    $die = ($level > 2);

    $opt = dmString::toArray($opt);

    if (!sfConfig::get('sf_debug') && !dmArray::get($opt, "force"))
    {
      return;
    }

    $tag = dmArray::get($opt, "tag", "pre");

    if (dmArray::get($opt, "to_string", false) && is_array($var))
    {
      array_walk_recursive($var, create_function(
        '&$val',
        'if(is_object($val)) {
          if (method_exists($val, "toString")) {
            $val = get_class($val)." : ".$val->toString();
          }
          elseif (method_exists($val, "__toString")) {
            $val = get_class($val)." : ".$val->__toString();
          }
        }'
        ));
    }
    elseif(is_array($var))
    {
      array_walk_recursive($var, create_function(
        '&$val',
        'if(is_object($val)) {
          if (method_exists($val, "toDebug")) {
            $val = get_class($val)." : ".print_r($val->toDebug(), true);
          }
          elseif (method_exists($val, "toArray")) {
            $val = get_class($val)." : ".print_r($val->toArray(), true);
          }
        }'
        ));
    }

    if(dmConfig::isCli())
    {
      $debugString = print_r($var, true);
      $debugString = substr($debugString, 0, self::MAX_DEBUG_LENGTH);
      echo $debugString;
      if (strlen($debugString) > self::MAX_DEBUG_LENGTH)
      {
        echo "\n---TRUNCATED---\n";
      }
      if($die) { die; }
    }
    else
    {

      array_walk_recursive($var, create_function(
      '&$val',
      'if(is_string($val)) { $val = htmlspecialchars($val); }'
      ));

      if (count($var) == 1)
      {
        $var = dmArray::first($var);
      }

      if (dmContext::hasInstance() && $request = dm::getRequest())
      {
        if ($request->isXmlHttpRequest())
        {
          echo "\n<$tag>";
          $debugString = print_r($var, true);
          echo substr($debugString, 0, self::MAX_DEBUG_LENGTH);
          if (strlen($debugString) > self::MAX_DEBUG_LENGTH)
          {
            echo "\n---TRUNCATED---\n";
          }
          echo "</$tag>\n";
          if ($die)
          die();
          return;
        }
      }
      ob_start();
      if ($level > 1)
      {
        print('<br /><'.$tag.' style="text-align: left; border: 1px solid #aaa; border-left-width: 10px; background-color: #f4F4F4; color: #000; margin: 3px; padding: 3px; font-size: 11px;">');

        $debugString = print_r($var, true);
        echo substr($debugString, 0, self::MAX_DEBUG_LENGTH);
        if (strlen($debugString) > self::MAX_DEBUG_LENGTH)
        {
          echo "\n---TRUNCATED---\n";
        }
        print("</$tag>");
      }
      $buffer = ob_get_clean();

      if ($level == 4)
      {
        ob_start();
        echo'<pre>';
        debug_print_backtrace();
        echo '</pre>';
        $dieMsg = ob_get_clean();
      }
      else
      {
        $backtrace = debug_backtrace();

        $dieMsg =
        str_replace(sfConfig::get("sf_root_dir"), "", dmArray::get($backtrace[1], 'file')).
          " l.".dmArray::get($backtrace[1], 'line');

        //      $dieMsg  = '<pre>';
        //      $dieMsg .= isset($backtrace[0]['file']) ?     '> file     : <b>'.
        //      $backtrace[1]['file'] .'</b>'. $CR : '';
        //      $dieMsg .= isset($backtrace[0]['line']) ?     '> line     : <b>'.
        //      $backtrace[1]['line'] .'</b>'. $CR : '';
        //      $dieMsg .= isset($backtrace[1]['class']) ?    '> class    : <b>'.
        //      dmArray::get(dmArray::get($backtrace, 2, array()), 'class') .'</b>'. $CR : '';
        //      $dieMsg .= isset($backtrace[1]['function']) ? '> function : <b>'.
        //      dmArray::get(dmArray::get($backtrace, 2, array()), 'function') .'</b>'. $CR : '';
        //      $dieMsg .= '</pre>';
      }

      if ($level > 1)
      {
        print($buffer);

        if ($die)
        die($dieMsg);
        else
        print($dieMsg);
      }
      else
      {
        sfWebDebug::getInstance()->logShortMessage($buffer.$dieMsg);
      }
    }
  }
}