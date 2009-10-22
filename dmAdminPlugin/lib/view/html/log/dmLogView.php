<?php

abstract class dmLogView
{
  protected
  $log,
  $i18n,
  $user,
  $dateFormat,
  $maxEntries = 10,
  $entries;
  
  public function __construct(dmLog $log, dmI18n $i18n, dmUser $user)
  {
    $this->log = $log;
    $this->i18n = $i18n;
    $this->user = $user;
    $this->dateFormat = new sfDateFormat($user->getCulture());
  }
  
  public function setMax($max)
  {
    $this->maxEntries = $max;
    $this->entries    = null;
    
    return $this;
  }
  
  public function render()
  {
    return
    $this->renderHead().
    '<tbody>'.$this->renderBody().'</tbody>'.
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
      $html .= '<th>'.$this->i18n->__(dmString::humanize($name)).'</th>';
    }
    
    $html .= '</tr></thead>';
    
    return $html;
  }
  
  public function renderBody()
  {
    $html = '';
    
    foreach($this->getEntries() as $index => $entry)
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
  
  protected function getEntries()
  {
    if(null === $this->entries)
    {
      $this->entries = $this->doGetEntries();
    }
    
    return $this->entries;
  }
  
  public function getHash()
  {
    $data = array();
    
    foreach($this->getEntries() as $entry)
    {
      $data[] = $entry->toArray();
    }
    
    return md5(serialize($data));
  }
  
  protected function doGetEntries()
  {
    return $this->log->getEntries($this->maxEntries);
  }
  
  public function renderFoot()
  {
    return '</table>';
  }
  
}