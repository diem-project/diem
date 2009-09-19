<?php

abstract class dmLogView
{
  protected
  $log,
  $dateFormat;
  
  public function __construct(dmLog $log, $culture)
  {
    $this->log = $log;
    $this->dateFormat = new sfDateFormat($culture);
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
      $html .= '<th>'.$name.'</th>';
    }
    
    $html .= '</tr></thead>';
    
    return $html;
  }
  
  public function renderBody($max = 20)
  {
    $html = '';
    
    foreach($this->log->getEntries($max) as $index => $entry)
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
  
  public function renderFoot()
  {
    return '</table>';
  }
  
}