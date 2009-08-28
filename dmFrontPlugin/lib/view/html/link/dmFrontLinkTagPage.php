<?php

class dmFrontLinkTagPage extends dmFrontLinkTag
{
  protected
  $page;

  protected function configure()
  {
    $this->page = $this->get('source');
    
    if (!$this->page instanceof DmPage)
    {
    	throw new dmException(sprintf('%s is not a valid DmPage', $this->page));
    }
    
    $this->set('tag', 'a');
  }

	protected function getBaseHref()
	{
		$pageSlug = $this->page->slug;
		return dm::getRequest()->getScriptName().($pageSlug ? '/'.$pageSlug : '');
	}

  protected function renderText()
  {
  	if (isset($this['text']))
  	{
  		return $this['text'];
  	}

  	return $this->page->name;
  }
  
  public function render()
  {
  	$preparedAttributes = $this->prepareAttributesForHtml($this->options);

  	$tagName = $preparedAttributes['tag'];
  	unset($preparedAttributes['tag']);
  	
  	if ($tagName == 'span')
  	{
  		unset($preparedAttributes['href'], $preparedAttributes['target']);
  	}
  	
  	$attributes = $this->convertAttributesToHtml($preparedAttributes);
    
    $tag = sprintf('<%s%s>%s</%s>',
      $tagName,
      $attributes,
      $this->renderText(),
      $tagName
    );
    
    return $tag;
  }
  
  protected function prepareAttributesForHtml(array $attributes)
  {
  	$attributes = parent::prepareAttributesForHtml($attributes);

    if($currentPage = dmContext::getInstance()->getPage())
    {
	    if ($currentPage->id === $this->page->id)
	    {
	      $attributes['class'][] = 'dm_current';
	      
	      if(sfConfig::get('dm_html_link_current_is_span', true))
	      {
	      	$attributes['tag'] = 'span';
	      }
	    }
	    elseif($currentPage->Node->isDescendantOf($this->page))
	    {
	      $attributes['class'][] = 'dm_parent';
	    }
    }
    
    return $attributes;
  }

}