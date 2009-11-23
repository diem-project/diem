<?php

class dmFormField extends sfFormField
{
  protected
  $isRequired,
  $htmlBuffer = '';

  public function __toString()
  {
    if ('' === $this->htmlBuffer)
    {
      return parent::__toString();
    }
    
    $return = $this->htmlBuffer;
    $this->htmlBuffer = '';
    return $return;
  }

  public function field($attributes = array())
  {
    $attributes = dmString::toArray($attributes);
    
    $attributes['class'] = dmArray::get($attributes, 'class', array());
    
    if ($this->isRequired)
    {
      $attributes['class'][] = 'required';
    }
    if ($this->error)
    {
      $attributes['class'][] = 'has_error';
    }
    
    $attributes['class'] = dmArray::toHtmlCssClasses($attributes['class']);
    
    $this->htmlBuffer .= parent::render($attributes);
    return $this;
  }

  public function label($label = null, $attributes = array())
  {
    $attributes = array_merge(
      array('class' => 'label'),
      dmString::toArray($attributes)
    );
    $label = null === $label ? $this->parent->getWidget()->getLabel($this->name) : $label;
    
    $this->htmlBuffer .= parent::renderLabel($label, $attributes);
    
    return $this;
  }

  public function error()
  {
    $this->htmlBuffer .= parent::renderError();
    return $this;
  }
  
  public function setIsRequired($val)
  {
    $this->isRequired = (bool) $val;
  }

}