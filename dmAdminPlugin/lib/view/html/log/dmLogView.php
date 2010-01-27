<?php

abstract class dmLogView extends dmConfigurable
{
  protected
  $log,
  $i18n,
  $user,
  $helper,
  $dateFormat,
  $maxEntries = 10,
  $entries;
  
  public function __construct(dmLog $log, dmI18n $i18n, dmCoreUser $user, dmHelper $helper, array $options = array())
  {
    $this->log = $log;
    $this->i18n = $i18n;
    $this->user = $user;
    $this->helper = $helper;

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'show_ip' => true
    );
  }

  protected function initialize(array $options)
  {
    $this->configure($options);

    $this->dateFormat = new sfDateFormat($this->user->getCulture());
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
    $html = '<table>';
    
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

  protected function renderIp($ip)
  {
    if ($this->getOption('show_ip'))
    {
      return $ip;
    }

    $ipParts = explode('.', $ip);
    return 4 === count($ipParts) ? $ipParts[0].'.'.$ipParts[1].'.xx.xx' : $ip;
  }
  
  protected function getEntries(array $options = array())
  {
    return $this->doGetEntries($options);
  }
  
  public function getHash()
  {
    return substr(md5(serialize($this->getEntries(array('hydrate' => false)))), -6);
  }
  
  protected function doGetEntries(array $options)
  {
    return $this->log->getEntries($this->maxEntries, $options);
  }
  
  public function renderFoot()
  {
    return '</table>';
  }
  
}