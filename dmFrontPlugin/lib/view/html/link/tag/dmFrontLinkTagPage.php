<?php

class dmFrontLinkTagPage extends dmFrontLinkTag
{
  protected
  $page,        // the page we link to
  $currentPage; // the page where the link is displayed

  public function __construct(dmFrontLinkResource $resource, DmPage $currentPage = null, array $requestContext, array $options = array())
  {
    $this->currentPage = $currentPage;
    
    parent::__construct($resource, $requestContext, $options);
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->page = $this->resource->getSubject();
    
    if (!$this->page instanceof DmPage)
    {
      throw new dmException(sprintf('%s is not a valid DmPage', $this->page));
    }
    
    $this->set('tag', 'a');
    
    if ($this->options['use_page_title'])
    {
      $this->title($this->page->_getI18n('title'));
    }
    
    $this->addAttributeToRemove(array('current_span', 'use_page_title'));
  }
  
  public function currentSpan($bool)
  {
    return $this->set('current_span', (bool) $bool);
  }
  
  protected function getBaseHref()
  {
    $pageSlug = $this->page->_getI18n('slug');
    
    $baseHref = $this->getHrefPrefix().($pageSlug ? '/'.$pageSlug : '');
    
    if(empty($baseHref))
    {
      $baseHref = '/';
    }
    
    return $baseHref;
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
      unset($preparedAttributes['href'], $preparedAttributes['target'], $preparedAttributes['title']);
    }
    
    $text = $this->renderText();
    
    if (isset($preparedAttributes['title']) && $preparedAttributes['title'] == $text)
    {
      unset($preparedAttributes['title']);
    }
    
    return '<'.$tagName.$this->convertAttributesToHtml($preparedAttributes).'>'.$text.'</'.$tagName.'>';
  }
  
  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    if (!sfConfig::get('dm_search_populating'))
    {
      if($this->currentPage)
      {
        if ($this->currentPage->get('id') === $this->page->get('id'))
        {
          $attributes['class'][] = 'dm_current';
          
          if($attributes['current_span'])
          {
            $attributes['tag'] = 'span';
          }
        }
        elseif($this->currentPage->getNode()->isDescendantOf($this->page))
        {
          $attributes['class'][] = 'dm_parent';
        }
      }
    }
    
    return $attributes;
  }

}