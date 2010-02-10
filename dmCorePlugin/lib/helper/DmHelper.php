<?php

function escape($text)
{
  return dmString::escape($text);
}

/**
 * @return dmLinkTag a link to $source
 */
function _link($source = null)
{
  return sfContext::getInstance()->getHelper()->link($source);
}
function £link($source = null)
{
  return _link($source);
}

/**
 * @return dmMediaTag
 */
function _media($source)
{
  return sfContext::getInstance()->getHelper()->media($source);
}
function £media($source)
{
  return _media($source);
}

/**
 * @return dmTableTag
 */
function _table()
{
  return sfContext::getInstance()->getHelper()->table();
}
function £table()
{
  return _table();
}

function _open($name, array $opt = array())
{
  return sfContext::getInstance()->getHelper()->open($name, $opt);
}
function £o($name, array $opt = array())
{
  return _open($name, $opt);
}

function _close($name)
{
  return sfContext::getInstance()->getHelper()->close($name);
}
function £c($name)
{
  return _close($name);
}

function _tag($name, $opt = array(), $content = false, $openAndClose = true)
{
  return sfContext::getInstance()->getHelper()->tag($name, $opt, $content, $openAndClose);
}
function £($name, $opt = array(), $content = false, $openAndClose = true)
{
  return _tag($name, $opt, $content, $openAndClose);
}

function dm_datetime($datetime)
{
  return trim($datetime, ' CEST');
}

function definition_list($array, $opt = array())
{
  $html = _open('dl', dmString::toArray($opt, true));

  foreach($array as $key => $value)
  {
    $html .= sprintf('<dt>%s</dt><dd>%s</dd>', __($key), $value);
  }

  $html .= '</dl>';

  return $html;
}

function plural($word, $nb, $showNb = true, $pluralSpec = false)
{
  $pluralizedWord = dmString::pluralizeNb($word, $nb, $pluralSpec);
  
  return $showNb ? $nb.' '.$pluralizedWord : $pluralizedWord;
}


function markdown($markdown, $opt = array())
{
  return _tag('div.markdown', dmString::toArray($opt), sfContext::getInstance()->get('markdown')->toHtml($markdown));
}

function unMarkdown($markdown)
{
  return sfContext::getInstance()->get('markdown')->toText($markdown);
}

function toggle($text = 'odd')
{
  sfConfig::set('dm_helper_toggle', sfConfig::get('dm_helper_toggle', 0)+1);
  return sfConfig::get('dm_helper_toggle')%2 ? $text : '';
}

function toggle_init($val = 0)
{
  sfConfig::set('dm_helper_toggle', $val);
}