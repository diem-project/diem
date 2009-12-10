<?php

/*
 * @return dmLinkTag a link to $source
 */
function _linkTag($source = null)
{
  return sfContext::getInstance()->getHelper()->£link($source);
}

/*
 * @return dmMediaTag
 */
function _media($source)
{
  return sfContext::getInstance()->getHelper()->£media($source);
}

/*
 * @return dmTableTag
 */
function _table()
{
  return sfContext::getInstance()->getHelper()->£table();
}

/*
 * a, class='tagada ergrg' id=zegf, contenu
 * a class=tagada id=truc, contenu
 * a, contenu
 * a, array(), contenu
 * a#truc.tagada, contenu
 */
function _tagO($name, array $opt = array())
{
  return sfContext::getInstance()->getHelper()->£o($name, $opt);
}

function _tagC($name)
{
  return sfContext::getInstance()->getHelper()->£c($name);
}

function _tag($name, $opt = array(), $content = false, $openAndClose = true)
{
  return sfContext::getInstance()->getHelper()->£($name, $opt, $content, $openAndClose);
}