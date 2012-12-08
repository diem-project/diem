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
function _table($opt = null)
{
  return sfContext::getInstance()->getHelper()->table($opt);
}
function £table($opt = null)
{
  return _table($opt);
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

/**
 * Will return the value only once per user session
 *
 * @param   mixed   $value the value to return only once
 * @param   string  $identifier an identifier to make this call unique
 *
 * @return  mixed   $value or null
 */
function once_per_session($value, $identifier = '')
{
  return sfContext::getInstance()->getUser()->oncePerSession($value, $identifier);
}

/**
 * Returns <script> tags for all javascripts associated with the given form.
 * @return string <script> tags
 */
function dm_get_javascripts_for_form(sfForm $form)
{
  $html = '';
  foreach ($form->getJavascripts() as $file)
  {
    $file = sfContext::getInstance()->getResponse()->calculateAssetPath('js', $file);
    $html .= javascript_include_tag($file);
  }

  return $html;
}

/**
 * Prints <script> tags for all javascripts associated with the given form.
 *
 * @see get_javascripts_for_form()
 */
function dm_include_javascripts_for_form(sfForm $form)
{
  echo dm_get_javascripts_for_form($form);
}

/**
 * Returns <link> tags for all stylesheets associated with the given form.
 * @return string <link> tags
 */
function dm_get_stylesheets_for_form(sfForm $form)
{
  $html = '';
  foreach ($form->getStylesheets() as $file => $media)
  {
    if(is_numeric($file) && is_string($media))
    {
      $file = $media;
      $media = 'all';
    }
    $file = sfContext::getInstance()->getResponse()->calculateAssetPath('css', $file);
    $html .= stylesheet_tag($file, array('media' => $media));
  }

  return $html;
}

/**
 * Prints <link> tags for all stylesheets associated with the given form.
 *
 * @see get_stylesheets_for_form()
 */
function dm_include_stylesheets_for_form(sfForm $form)
{
  echo get_stylesheets_for_form($form);
}

/**
 * Renders partial from module
 * 
 * @param string $module Module name
 * @param string $template_name Partial to render
 * @param array $parameters The array of parameters for partial to render
 */
function dm_render_partial($module, $template_name, array $parameters = array()) 
{
    echo dmContext::getInstance()->getHelper()->renderPartial($module, $template_name, $parameters);
}