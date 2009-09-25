<?php

abstract class dmLogView
{
  protected
  $log,
  $i18n,
  $user,
  $dateFormat;
  
  public function __construct(dmLog $log, dmI18n $i18n, dmUser $user)
  {
    $this->log = $log;
    $this->i18n = $i18n;
    $this->user = $user;
    $this->dateFormat = new sfDateFormat($user->getCulture());
  }
  
  public function render($max = 20)
  {
    return
    $this->renderHead().
    '<tbody>'.$this->renderBody($max).'</tbody>'.
    $this->renderFoot();
  }
  
  public function renderEmpty()
  {
    return
    $this->renderHead().
    '<tbody></tbody>'.
    $this->renderFoot();
  }
  
  public function renderHead()
  {
    $html = '<table><thead><tr>';
    
    foreach($this->rows as $name => $method)
    {
      $html .= '<th>'.$this->i18n->__($name).'</th>';
    }
    
    $html .= '</tr></thead>';
    
    return $html;
  }
  
  public function renderBody($max = 20)
  {
    $html = '';
    
    foreach($this->getEntries($max) as $index => $entry)
    {
      $html .= '<tr class="'.($index%2 ? 'odd' : 'even').'">';
      
      foreach($this->rows as $name => $method)
      {
        $html .= '<td>'.$this->$method($entry).'</td>';
      }
      
      $html .= '</tr>';
    }
    
    return $html;
  }
  
  protected function getEntries($max)
  {
    return $this->log->getEntries($max);
  }
  
  public function renderFoot()
  {
    return '</table>';
  }
  
}