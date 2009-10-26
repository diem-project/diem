<?php

class dmOs
{
  const INTERNET_CHECK_HOST = '216.239.59.104';

  protected static
  $isInternetAvailable;

  /*
   * Builds a path with many path parts
   *
   * dmOs("/home/user/", "/dir", "sub_dir", "file.ext")
   * returns "/home/user/dir/sub_dir/file.ext"
   */
  public static function join()
  {
    $parts = func_get_args();

    /*
     * Join path parts with $separator
     */
    $dirtyPath = implode('/', $parts);
    
    if(strpos($dirtyPath, '//') !== false)
    {
      $dirtyPath = preg_replace('|(/{2,})|', '/', $dirtyPath);
    }

    $cleanPath = '/'.trim($dirtyPath, '/');
    
    return $cleanPath;
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
    $extension = dmArray::get(pathinfo($file), 'extension');
    return $widthDot ? '.'.$extension : $extension;
  }

  public static function getFileWithoutExtension($file)
  {
    return dmArray::get(pathinfo($file), 'filename');
  }

  public static function getFileMime($file)
  {
    $fileExtension = self::getFileExtension($file, false);

    //This will set the Content-Type to the appropriate setting for the file
    switch(strtolower($fileExtension))
    {
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "bmp": $ctype="image/bmp"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpeg"; break;
      case "mp3": $ctype="audio/mpeg"; break;
      case "ogg": $ctype="audio/mpeg"; break;
      case "wav": $ctype="audio/x-wav"; break;
      case "mpeg":
      case "mpg":
      case "mpe": $ctype="video/mpeg"; break;
      case "mp4": $ctype="video/mpeg"; break;
      case "flv": $ctype="video/x-flv"; break;
      case "mov": $ctype="video/quicktime"; break;
      case "avi": $ctype="video/x-msvideo"; break;
      case "swf": $ctype="application/x-shockwave-flash"; break;
      //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
      case "php":
      default:
        $ctype="application/force-download";
    }
    return $ctype;
  }

  public static function sanitizeDirName($file)
  {
    return trim(
      preg_replace('/[^\w\._-]+/i', '-', dmString::removeAccents($file)),
      '-'
    );
  }

  public static function sanitizeFileName($file)
  {
    return trim(
      preg_replace("|[".preg_quote('\'*"/\[]:;|=,', '|')."]+|i", '-', $file)
    );
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

  public static function isInternetAvailable()
  {
    if (null === self::$isInternetAvailable)
    {
      if($fp = @fsockopen(self::INTERNET_CHECK_HOST, 80, $errno, $errstring))
      {
        fclose($fp);
        self::$isInternetAvailable = true;
      }
      else
      {
        self::$isInternetAvailable = false;
      }
    }
    
    return self::$isInternetAvailable;
  }
  
  public static function getPerformanceInfos()
  {
    return array(
      'usage' => memory_get_usage(true) / (1024*1024),
      'peak' => memory_get_peak_usage(true) / (1024*1024),
      'max' => ini_get('memory_limit'),
      'time' => round(1000*(microtime(true) - dm::getStartTime()))
    );
  }

}