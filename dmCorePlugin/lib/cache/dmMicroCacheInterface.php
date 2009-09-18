<?php

interface dmMicroCacheInterface
{
  public function getCache($key);

  public function setCache($key, $value);

  public function clearCache($key = null);

  public function hasCache($key);

}