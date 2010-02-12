<?php

function _tagO($name, array $opt = array())
{
  return _open($name, $opt);
}

function _tagC($name)
{
  return _close($name);
}