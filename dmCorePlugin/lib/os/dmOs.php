<?php

class dmOs
{
  /**
   * Builds a path with many path parts
   *
   * dmOs("/home/user/", "/dir", "sub_dir", "file.ext")
   * returns "/home/user/dir/sub_dir/file.ext"
   */
  public static function join()
  {
    $parts = func_get_args();

    $dirtyPath = implode('/', $parts);
    
    if(strpos($dirtyPath, '//') !== false)
    {
      $dirtyPath = preg_replace('|(/{2,})|', '/', $dirtyPath);
    }
    
    $cleanPath = trim($dirtyPath, '/');
    
    if ('/' === DIRECTORY_SEPARATOR)
    {
      $cleanPath = '/'.$cleanPath;
    }
    else
    {
      $cleanPath = self::normalize($cleanPath);
    }

    return $cleanPath;
  }
  
  public static function normalize($path)
  {
    if ('/' !== DIRECTORY_SEPARATOR)
    {
      $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }
    
    return $path;
  }

  static function isLocalhost()
  {
    return
    $_SERVER["HTTP_HOST"] === "127.0.0.1"
    || $_SERVER["HTTP_HOST"] === "localhost"
    || strncmp($_SERVER["HTTP_HOST"], "192.168.", 8) === 0;
  }

  public static function randomizeFileName($file, $maxLen = 255)
  {
    $random = dmString::random(4);
    $ext = self::getFileExtension($file, true);
    $strip_len = $maxLen - strlen($random) - strlen($ext);

    $strip_name = dmString::truncate(self::sanitizeFileName(self::getFileWithoutExtension($file)), $strip_len, "");

    $final_name = $strip_name."-".$random.$ext;

    return $final_name;
  }

  public static function randomizeDirName($dir, $maxLen = 255)
  {
    $random = dmString::random(4);

    $strip_len = $maxLen - strlen($random);

    $strip_name = dmString::truncate(self::sanitizeDirName($file), $strip_len, "");

    $final_name = $strip_name."-".$random;

    return $final_name;
  }

  public static function getFileExtension($file, $widthDot = true)
  {
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    return $widthDot ? '.'.$extension : $extension;
  }

  public static function getFileWithoutExtension($file)
  {
    return pathinfo($file, PATHINFO_FILENAME);
  }

  public static function sanitizeDirName($dirName)
  {
    return trim(preg_replace('/[^\w\._-]+/i', '-', dmString::transliterate($dirName)), '-');
  }

  public static function sanitizeFileName($file)
  {
    return trim(preg_replace("|[".preg_quote('\'*"/\[]:;|=,', '|')."]+|i", '-', $file));
  }

  public static function humanizeSize($file)
  {
    $bytes = is_numeric($file)
    ? $file
    : filesize($file);
    if ($bytes>(1024*1024))
    {
      $size=(round($bytes/(1024*1024), 1))." Mo";
    }
    else
    {
      if ($bytes>1024)
      {
        $size=(round($bytes/1024))." Ko";
      }
      else
      {
        $size=$bytes." o";
      }
    }
    return $size;
  }

  
  public static function getPerformanceInfos()
  {
    return array(
      'usage' => memory_get_usage(true) / (1024*1024),
      'peak'  => memory_get_peak_usage(true) / (1024*1024),
      'max'   => ini_get('memory_limit'),
      'time'  => round(1000*(microtime(true) - dm::getStartTime()))
    );
  }

}