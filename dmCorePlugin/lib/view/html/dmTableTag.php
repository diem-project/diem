<?php

class dmTableTag extends dmHtmlTag
{
  protected
  $helper,
  $head,
  $body,
  $foot,
  $useStrip = false,
  $stripCount,
  $caption;
  
  public function __construct(dmHelper $helper)
  {
    $this->helper = $helper;
    
    $this->initialize();
  }

  public function getDefaultOptions()
  {
    return array();
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->head = $this->body = $this->foot = array();
  }

  public function useStrip($value = null)
  {
    if (null === $value)
    {
      return $this->useStrip;
    }

    $this->useStrip = (bool) $value;

    return $this;
  }
  
  public function clearBody()
  {
    $this->body = array();
  }
  
  public function render()
  {
    return $this->helper->tag('table', $this->options,
      $this->renderCaption().
      $this->renderHead().
      $this->renderFoot().
      $this->renderBody()
    );
  }

  public function renderCaption()
  {
    if(is_null($this->caption))
    {
      return '';
    }
    else
    {
      return $this->helper->tag('caption', $this->caption);
    }
  }

  public function renderHead()
  {
    return $this->renderPart($this->head, 'thead', 'th');
  }
  
  public function renderBody()
  {
    return $this->renderPart($this->body, 'tbody', 'td');
  }
  
  public function renderFoot()
  {
    return $this->renderPart($this->foot, 'tfoot', 'th');
  }
  
  protected function renderPart(array $rows, $partTag, $cellTag)
  {
    if (empty($rows))
    {
      return '';
    }
    
    $this->stripCount = 0;
    
    $html = '<'.$partTag.'>';

    foreach($rows as $row)
    {
      $html .= $this->renderRow($row, $cellTag);
    }
    
    $html .= '</'.$partTag.'>';
    
    return $html;
  }
  
  protected function renderRow(array $row, $cellTag)
  {
    if ($this->useStrip && 'td' === $cellTag)
    {
      $open = '<tr class="'.((++$this->stripCount % 2) ? 'even' : 'odd').'">';
    }
    else
    {
      $open = '<tr>';
    }
    
    return $open.'<'.$cellTag.'>'.implode('</'.$cellTag.'><'.$cellTag.'>', $row).'</'.$cellTag.'></tr>';
  }

  public function caption($caption)
  {
    $this->caption = $caption;
    return $this;
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