<?php

class dmFilesystem extends sfFilesystem
{
  protected
    $dispatcher,
    $lastExec; // array(command, output, return)

  public function mkdir($path, $mode = 0777)
  {
    if (!is_dir($path))
    {
      $oldUmask = umask(0);
      mkdir($path, $mode, true);
      umask($oldUmask);
    }

    return is_writable($path);
  }

  public function touch($file, $mode = 0777)
  {
    if (file_exists($file))
    {
      if (!is_writable($file))
      {
        chmod($file, $mode);
        return is_writable($file);
      }
      return true;
    }
    if (touch($file))
    {
      chmod($file, $mode);
      return is_writable($file);
    }
    return false;
  }

  public function find($type = "any")
  {
    return sfFinder::type($type);
  }

  public function getFileInfos($file)
  {
    if (!file_exists($file))
    {
      return '[x]';
    }
    $username = function_exists('posix_getpwuid')
    ? dmArray::get(@posix_getpwuid(dmArray::get(stat($file), "uid")), "name")
    : '';
    $permissions = substr(decoct(fileperms($file)), 2);

    return $username.":".$permissions;
  }

//  public function execOld($command)
//  {
//    exec($command, $output, $returnCode);
//    
//    $this->lastExec = array(
//      'command' => $command,
//      'output' => implode("\n", $output),
//      'return' => $returnCode
//    );
//    
//    return 0 === $returnCode;
//  }
  
  public function exec($command, $stdoutCallback = null, $stderrCallback = null)
  {
    try
    {
      list($out, $err) = $this->execute($command, $stdoutCallback, $stderrCallback);
    }
    catch(RuntimeException $e)
    {
      $this->lastExec = array(
        'command' => $command,
        'output'  => $e->getMessage()
      );
      
      return false;
    }
    
    $this->lastExec = array(
      'command' => $command,
      'output'  => $out,
    );
    
    return true;
  }

  public function sf($command)
  {
    try
    {
      $phpCli = sfToolkit::getPhpCli();
    }
    catch(sfException $e)
    {
      $this->lastExec = array(
        'command' => $command,
        'output'  => $e->getMessage()
      );
      
      return false;
    }
    
    $sfCommand = sprintf(
      '%s "%s" %s',
      $phpCli,
      sfConfig::get('sf_root_dir').'/symfony',
      $command
    );
    
    return $this->exec($sfCommand);
  }

  public function getLastExec($key = null)
  {
    if (null === $key)
    {
      return $this->lastExec;
    }
    
    return dmArray::get($this->lastExec, $key);
  }


  // truncate folder
  public function deleteDirContent($dir, $throwExceptions = false)
  {
    if (!dmProject::isInProject($dir))
    {
      throw new dmException(sprintf('Try to delete %s wich is outside symfony project', $dir));
    }

    $success = true;

    if(!$dh = @opendir($dir))
    {
      if ($throwExceptions)
      {
        throw new dmException("Can not open $dir folder");
      }
      else
      {
        $success = false;
      }
    }
    while (false !== ($obj = @readdir($dh)))
    {
      if($obj == '.' || $obj == '..')
      {
        continue;
      }

      if (is_dir($dir . '/' . $obj))
      {
        $success &= $this->deleteDir($dir.'/'.$obj, $throwExceptions);
      }
      else
      {
        if (!@unlink($dir . '/' . $obj))
        {
          if ($throwExceptions)
          {
            throw new dmException("Can not delete file $dir/$obj");
          }
          else
          {
            $success = false;
          }
        }
      }
    }
    @closedir($dh);

    return $success;
  }

  // destroy folder
  public function deleteDir($dir, $throwExceptions = false)
  {
    if ($success = $this->deleteDirContent($dir, $throwExceptions))
    {
      if (!@rmdir($dir))
      {
        if ($throwExceptions)
        {
          throw new sfException("Can not delete folder $dir");
        }
        else
        {
          $success = false;
        }
      }
    }

    return $success;
  }

  public function unlink($files)
  {
    if (!is_array($files))
    {
      $files = array($files);
    }
    $success = true;
    $files = array_reverse($files);
    foreach ($files as $file)
    {
      if (is_dir($file) && !is_link($file))
      {
        $success &= $this->deleteDir($file);
      }
      elseif(is_file($file))
      {
        $success &= @unlink($file);
      }
    }
    return $success;
  }
  
  /**
   * Calculates the relative path from one to another directory.
   * If they share no common path the absolute target dir is returned
   *
   * @param string $from directory from that the relative path shall be calculated
   * @param string $to target directory
   */ 
  public function getRelativeDir($from, $to)
  {
    /*
     * $from must end with /
     */
    $from = '/'.trim($from, '/').'/';
    return $this->calculateRelativeDir($from, $to);
  }
}