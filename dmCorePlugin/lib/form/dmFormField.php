<?php

class dmFormField extends sfFormField
{
  protected
  $isRequired,
  $htmlBuffer = null;

  public function __toString()
  {
    if (null === $this->htmlBuffer)
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

    $parentAttributes = $this->parent->getWidget()->offsetGet($this->name)->getAttributes();
    
    $attributes['class'] = array_merge(
      (array) dmArray::get($parentAttributes, 'class'),
      dmArray::get($attributes, 'class', array())
    );
    
    if ($this->isRequired)
    {
      $attributes['class'][] = 'required';
    }
    if ($this->error)
    {
      $attributes['class'][] = 'has_error';
    }
    
    $attributes['class'] = dmArray::toHtmlCssClasses($attributes['class']);

    $this->htmlBuffer .= parent::render(array_merge($parentAttributes, $attributes));
    
    return $this;
  }

  public function label($label = null, $attributes = array())
  {
    $attributes = dmString::toArray($attributes);
    
    $attributes['class'] = dmArray::toHtmlCssClasses(
      empty($attributes['class'])
      ? array('label')
      : array_merge((array) $attributes['class'], array('label'))
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

  public function help($help = null)
  {
    if (null === $this->parent)
    {
      throw new LogicException(sprintf('Unable to render the help for "%s".', $this->name));
    }
    
    $help = null === $help ? $this->parent->getWidget()->getHelp($this->name) : $help;

    $this->htmlBuffer .= $this->parent->getWidget()->getFormFormatter()->formatHelp($help);

    return $this;
  }
  
  public function setIsRequired($val)
  {
    $this->isRequired = (bool) $val;
  }

  public function getHelp()
  {
    return $this->parent->getWidget()->getHelp($this->name);
  }
}