<?php

class dmFrontLinkTagPage extends dmFrontLinkTag
{
  protected
  $user,
  $page,        // the page we link to
  $currentPage; // the page where the link is displayed

  public function __construct(dmFrontLinkResource $resource, array $requestContext, DmPage $currentPage = null, dmCoreUser $user = null, array $options = array())
  {
    $this->currentPage = $currentPage;
    $this->user = $user;
    
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
    
    $this->setOption('tag', 'a');
    
    if ($this->options['use_page_title'])
    {
      $this->title($this->page->_getI18n('title'));
    }
    
    $this->addAttributeToRemove(array('use_page_title'));
  }

  /*
   * @return DmPage the page we are linking to
   */
  public function getPage()
  {
    return $this->page;
  }

  public function isCurrent()
  {
    return $this->currentPage && $this->currentPage->get('id') === $this->page->get('id');
  }

  public function isCurrentStrict()
  {
	$reqContext = $this->requestContext;
	$relativeToRootRequestUri = str_replace($reqContext['uri_prefix'].$reqContext['prefix'], '', $reqContext['request_uri']);
	return ltrim($relativeToRootRequestUri,'/') == $this->currentPage->getSlug() || $relativeToRootRequestUri == 'http://'; //fix CLI tests...
  }

  public function isParent()
  {
    return $this->currentPage && $this->currentPage->getNode()->isDescendantOf($this->page);
  }
  
  protected function getBaseHref()
  {
    $pageSlug = $this->page->_getI18n('slug');
    $basePrefix = $this->getHrefPrefix();
    
    $prefixI18n = sfConfig::get('dm_i18n_prefix_url');
    if($prefixI18n){
      $culture = $this->user->getCulture();
      $baseHref = $basePrefix . '/' . $culture . ($pageSlug ? '/' .$pageSlug : '');
    }else{
      $baseHref = $this->getHrefPrefix().($pageSlug ? '/'.$pageSlug : '');
    }
    
    if(empty($baseHref))
    {
      $baseHref = $prefixI18n ? '/'.$culture : '/';
    }
    
    return $baseHref;
  }

  protected function renderText()
  {
    if (isset($this->options['text']))
    {
      return $this->options['text'];
    }

    return dmString::escape($this->page->_getI18n('name'));
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
    
    if (isset($preparedAttributes['title']) && dmString::strtolower($preparedAttributes['title']) == dmString::strtolower($text))
    {
      unset($preparedAttributes['title']);
    }

    return $this->doRender($tagName, $this->convertAttributesToHtml($preparedAttributes), $text);
  }
  
  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);

    if (!sfConfig::get('dm_search_populating'))
    {
      // inactive page
      if($this->user && !$this->page->_getI18n('is_active') && !$this->user->can('site_view'))
      {
        $attributes['class'][] = 'dm_inactive';
        $attributes['tag'] = 'span';
      }

      // current page
      if($this->isCurrent())
      {
        $attributes['class'][] = $attributes['current_class'];
          
        if(empty($attributes['anchor']) && $attributes['current_span'] && $this->isCurrentStrict())
        {
          $attributes['tag'] = 'span';
        }
      }
      // parent page
      elseif($this->isParent())
      {
        $attributes['class'][] = $attributes['parent_class'];
      }
    }
    
    return $attributes;
  }

}