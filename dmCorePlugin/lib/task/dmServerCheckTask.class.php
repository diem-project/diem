<?php

class dmServerCheckTask extends sfBaseTask
{
  protected
  $rowWidth = array(30, 25, 25, 12),
  $errors,
  $warnings,
  $checks;
  
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
    ));
    
    $this->namespace = 'dm';
    $this->name = 'server-check';
    $this->briefDescription = 'Verify if the server matches both Symfony & Diem requirements';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    require_once(realpath(dirname(__FILE__).'/../os/dmServerCheck.php'));
    
//    echo "** WARNING **\n";
//    echo "*  The PHP CLI can use a different php.ini file\n";
//    echo "*  than the one used with your web server.\n";
//    echo "*  If this is the case, please launch this\n";
//    echo "*  utility from your web server : http://mysite.com/dm_check.php\n";
//    echo "** WARNING **\n";
    
    $this->warnings = array();
    $this->errors = array();
    $this->checks = array();
    
    $serverCheck = new dmServerCheck;
    
    foreach($serverCheck->getChecks() as $checkSpace => $checks)
    {
      $this->renderHead($checkSpace);
      
      foreach($checks as $check)
      {
        $this->renderCheck($check);
      
        if ('error' === $check->getDiagnostic())
        {
          $this->errors[] = $check;
        }
        elseif ('warning' === $check->getDiagnostic())
        {
          $this->warnings[] = $check;
        }
        
        $this->checks[] = $check;
        
        usleep(200000);
      }
    }
  
    if (count($this->warnings))
    {
      $this->logBlock(count($this->warnings).' warnings : '.implode(', ', $this->warnings), 'COMMENT_LARGE');
    }
    if(count($this->errors))
    {
      $this->logBlock(sprintf('%d/%d check(s) failed : '.implode(', ', $this->errors), count($this->errors), count($this->checks)), 'ERROR_LARGE');
    }
    else
    {
      $this->logBlock('The server matches Symfony & Diem requirements', 'INFO_LARGE');
    }
    
    if(count($this->errors))
    {
      throw new dmServerCheckException('Diem can NOT run safely in this environment');
    }
  }
  
  protected function renderHead($space)
  {
    return $this->logBlock($this->renderLine(array_map('strtoupper', array(
      $space,
      'Requirement',
      'Server state',
      'Diagnostic'
    ))), 'COMMENT_LARGE');
  }
  
  protected function renderCheck(dmServerCheckUnit $check)
  {
    $line = $this->renderLine(array(
      $check->renderName(),
      $check->renderRequirement(),
      $check->renderState(),
      str_replace('valid', 'ok', $check->getDiagnostic())
    ));
    
    switch($check->getDiagnostic())
    {
      case 'valid':   $this->logBlock($line, 'INFO'); break;
      case 'warning': $this->log(' '.$line); break;
      case 'error':   $this->logBlock($line, 'ERROR'); break;
    }
  }
  
  protected function renderLine(array $values)
  {
    $text = '';
    
    foreach($values as $index => $value)
    {
      $text.= str_repeat(' ', max(0, $this->rowWidth[$index] - strlen($value))).$value;
    }
    
    return $text;
  }
  
}