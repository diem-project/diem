<?php

abstract class dmHtmlTag
{

	protected
	  $options = array('class' => array()),
    $attributesToRemove = array(),
    $emptyAttributesToRemove = array('class');

	abstract public function render();

  public function __toString()
  {
  	try
  	{
      $string = $this->render();
  	}
  	catch(Exception $e)
  	{
  		if (sfConfig::get('sf_debug'))
  		{
  		  $string = $e->getMessage();
  		}
  		else
  		{
  			$string = ' ';
  		}
  	}
  	return $string;
  }

	public function set($name, $value = null)
	{
		if(is_array($name))
		{
			foreach($name as $n => $v)
			{
				$this->set($n, $v);
			}
		}
		elseif (null !== $value)
		{
			$this->options[$name] = $value;
		}
		/*
		 * As value is null,
		 * name probably contains inlined data
		 */
		else
		{
	    if ($first_space_pos = strpos($name, " "))
	    {
	      $opt_string = substr($name, $first_space_pos + 1);
	      $name = substr($name, 0, $first_space_pos);
	      // DMS STYLE - string opt in name
	      dmString::retrieveOptFromString($opt_string, $this->options);
	    }

	    // JQUERY STYLE - css expression
	    dmString::retrieveCssFromString($name, $this->options);
		}
		
    return $this;
	}
	
	/*
	 * get an option by key
	 * @return mixed option value or default
	 */
	public function get($key, $default = null)
	{
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}

  public function addClass($class)
  {
    $this->options['class'][] = $class;
    return $this;
  }

  public function removeClass($class)
  {
    if($this->hasClass($class))
    {
    	unset($this['class'][$class]);
    }
    return $this;
  }

  public function hasClass($class)
  {
    return in_array($class, $this['class']);
  }

  public function json($data)
  {
    return $this->addClass(str_replace('"', "'", json_encode($data)));
  }

  protected function getHtmlAttributes()
  {
  	return $this->convertAttributesToHtml($this->prepareAttributesForHtml($this->options));
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    return $attributes;
  }

  protected function convertAttributesToHtml(array $attributes)
  {
  	/*
  	 * Implode classes
  	 */
    if (isset($attributes['class']))
    {
      $attributes['class'] = dmArray::toHtmlCssClasses($attributes['class']);
    }
    
    /*
     * Remove non html attributes
     */
    foreach($this->attributesToRemove as $key)
    {
    	if (isset($attributes[$key]))
    	{
    		unset($attributes[$key]);
    	}
    }
    
    /*
     * Remove empty attributes
     */
    $attributes = dmArray::unsetEmpty($attributes, $this->emptyAttributesToRemove);
    
    /*
     * Convert attributes array into html string params
     */
  	$htmlAttributesString = '';
    foreach ($attributes as $key => $value)
    {
      if (null !== $value)
      {
//      	if(is_array($value))
//      	{
//      		dmDebug::kill($attributes, $key, $value);
//      	}
        $htmlAttributesString .= ' '.$key.'="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'"';
      }
    }
    
    return $htmlAttributesString;
  }

  protected function addAttributeToRemove($attribute)
  {
    $this->attributesToRemove = array_merge(
      $this->attributesToRemove,
      (array) $attribute
    );
  }
  
  protected function addEmptyAttributeToRemove($attribute)
  {
    $this->emptyAttributesToRemove = array_merge(
      $this->emptyAttributesToRemove,
      (array) $attribute
    );
  }


}