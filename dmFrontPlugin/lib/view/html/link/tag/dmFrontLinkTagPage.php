<?php

class dmFrontLinkTagPage extends dmFrontLinkTag
{
  protected
  $page;

  protected function initialize()
  {
    parent::initialize();
    
    $this->page = $this->resource->getSubject();
    
    if (!$this->page instanceof DmPage)
    {
      throw new dmException(sprintf('%s is not a valid DmPage', $this->page));
    }
    
    $this->set('tag', 'a');
  }

  protected function getBaseHref()
  {
    $pageSlug = $this->page->_getI18n('slug');
    
    return $this->requestContext['script_name'].($pageSlug ? '/'.$pageSlug : '');
  }

  protected function renderText()
  {
    if (isset($this->options['text']))
    {
      return $this->options['text'];
    }

    return $this->page->_getI18n('name');
  }
  
  public function render()
  {
    $preparedAttributes = $this->prepareAttributesForHtml($this->options);

    $tagName = $preparedAttributes['tag'];
    unset($preparedAttributes['tag']);
    
    if ($tagName === 'span')
    {
      unset($preparedAttributes['href'], $preparedAttributes['target']);
    }
    
    return '<'.$tagName.$this->convertAttributesToHtml($preparedAttributes).'>'.$this->renderText().'</'.$tagName.'>';
  }
  
  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    if (!sfConfig::get('dm_search_populating'))
    {
      if($currentPage = self::$context->getPage())
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
    }
    
    return $attributes;
  }

}