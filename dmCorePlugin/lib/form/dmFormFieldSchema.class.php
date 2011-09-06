<?php

class dmFormFieldSchema extends sfFormFieldSchema
{
  public function offsetGet($name)
  {
    if (!isset($this->fields[$name]))
    {
      if (null === $widget = $this->widget[$name])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      $error = isset($this->error[$name]) ? $this->error[$name] : null;

      if ($widget instanceof sfWidgetFormSchema)
      {
        $class = 'dmFormFieldSchema';

        if ($error && !$error instanceof sfValidatorErrorSchema)
        {
          $error = new sfValidatorErrorSchema($error->getValidator(), array($error));
        }
      }
      else
      {
        $class = 'dmFormField';
      }

      $this->fields[$name] = new $class($widget, $this, $name, isset($this->value[$name]) ? $this->value[$name] : null, $error);
    }

    return $this->fields[$name];
  }
}