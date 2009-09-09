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
		$pageSlug = $this->page->_getI18n('slug');
		
		return dm::getRequest()->getScriptName().($pageSlug ? '/'.$pageSlug : '');
	}

  protected function renderText()
  {
  	if (isset($this['text']))
  	{
  		return $this['text'];
  	}

  	return $this->page->_getI18n('name');
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
	    if ($currentPage->get('id') === $this->page->get('id'))
	    {
	      $attributes['class'][] = 'dm_current';
	      
	      if(dmConfig::get('link_current_span', true))
	      {
	      	$attributes['tag'] = 'span';
	      }
	    }
	    elseif($currentPage->getNode()->isDescendantOf($this->page))
	    {
	      $attributes['class'][] = 'dm_parent';
	    }
    }
    
    return $attributes;
  }

}