<?php

class dmTableTag extends dmHtmlTag
{
  protected
  $head,
  $body,
  $foot;
  
  protected static
  $toggler;
  
  public function __construct()
  {
    $this->initialize();
  }
  
  protected function initialize(array $options = array())
  {
    parent:: initialize($options);
    
    $this->head = $this->body = $this->foot = array();
  }
  
  public function clearBody()
  {
    $this->body = array();
  }
  
  public function render()
  {
    return '<table>'.$this->renderHead().$this->renderFoot().$this->renderBody().'</table>';
  }
  
  public function renderHead()
  {
    return $this->renderPart($this->head, 'thead', 'th');
  }
  
  public function renderBody()
  {
    return $this->renderPart($this->body, 'tbody', 'td', array('toggle' => true));
  }
  
  public function renderFoot()
  {
    return $this->renderPart($this->foot, 'tfoot', 'th');
  }
  
  protected function renderPart(array $rows, $partTag, $cellTag, array $options = array())
  {
    if (empty($rows))
    {
      return '';
    }
    
    $options = array_merge(array(
      'toggle' => false
    ), $options);
    
    self::$toggler = 0;
    
    $html = '<'.$partTag.'>';
    foreach($rows as $row)
    {
      $html .= $this->renderRow($row, $cellTag, $options);
    }
    $html .= '</'.$partTag.'>';
    
    return $html;
  }
  
  protected function renderRow(array $row, $cellTag, array $options)
  {
    if ($options['toggle'])
    {
      $open = '<tr class="'.((++self::$toggler%2) ? 'even' : 'odd').'">';
    }
    else
    {
      $open = '<tr>';
    }
    
    return $open.'<'.$cellTag.'>'.implode('</'.$cellTag.'><'.$cellTag.'>', $row).'</'.$cellTag.'></tr>';
  }

  public function head()
  {
    $this->head[] = $this->validateRowArgs(func_get_args());
    return $this;
  }

  public function body()
  {
    $this->body[] = $this->validateRowArgs(func_get_args());
    return $this;
  }

  public function foot()
  {
    $this->foot[] = $this->validateRowArgs(func_get_args());
    return $this;
  }
  
  protected function validateRowArgs($args)
  {
    if(1 == count($args))
    {
      $args = (array) $args[0];
    }
    
    foreach($args as $index => $arg)
    {
      $args[$index] = (string) $arg;
    }
    
    return $args;
  }
}