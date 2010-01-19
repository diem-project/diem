<?php

class dmFrontPagerView extends dmConfigurable implements Iterator, Countable
{
  protected
  $pager,
  $context,
  $helper,
  $baseHref,
  $navigationCache = array();

  public function __construct(dmContext $context, array $options = array())
  {
    $this->context  = $context;
    $this->helper   = $context->getHelper();

    $this->initialize($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'navigation_top'    => true,
      'navigation_bottom' => true,
      'separator'         => null,
      'class'             => null,
      'current_class'     => 'current',
      'first'             => dmString::escape('<<'),
      'prev'              => dmString::escape('<'),
      'next'              => dmString::escape('>'),
      'last'              => dmString::escape('>>'),
      'nb_links'          => 9,
      'ajax'              => false,
      'widget_id'         => null   // only used when ajax = true
    );
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  protected function initBaseHref()
  {
    $this->setBaseHref(($page = $this->context->getPage())
    ? $this->helper->£link($page)->getAbsoluteHref()
    : preg_replace('|/page/([0-9]+)|', '?page=$1', $this->context->getRequest()->getUri())
    );
  }

  public function setBaseHref($baseHref)
  {
    $this->baseHref = $baseHref;

    return $this;
  }

  public function getBaseHref()
  {
    return $this->baseHref;
  }

  public function setPager(sfPager $pager)
  {
    $this->pager = $pager;

    return $this;
  }

  public function renderNavigationTop($options = array())
  {
    if ($this->getOption('navigation_top'))
    {
      return $this->renderNavigation($options);
    }
  }

  public function renderNavigationBottom($options = array())
  {
    if ($this->getOption('navigation_bottom'))
    {
      return $this->renderNavigation($options);
    }
  }

  public function renderNavigation($options = array())
  {
    if(!$this->pager->haveToPaginate())
    {
      return '';
    }
    
    if(!empty($options))
    {
      $this->setOptions(array_merge($this->getOptions(), dmString::toArray($options, true)));
    }

    $this->initBaseHref();
    
    $cacheKey = md5(var_export($this->getOptions(), true).$this->getBaseHref());

    if (isset($this->navigationCache[$cacheKey]))
    {
      return $this->navigationCache[$cacheKey];
    }

    $html =
    $this->openPager().
    $this->renderFirstAndPreviousLinks().
    $this->renderPageLinks().
    $this->renderNextAndLastLinks().
    $this->closePager();

    if($this->getOption('ajax'))
    {
      $this->context->getResponse()->addJavascript('front.ajaxPager');
    }

    $this->navigationCache[$cacheKey] = $html;

    return $html;
  }

  protected function openPager()
  {
    return
    $this->helper->£o('div', array('class' => dmArray::toHtmlCssClasses(array(
      'pager',
      $this->getOption('class'),
      $this->getOption('ajax') ? 'dm_pager_ajax_links' : null
    )))).
    $this->helper->£o('ul.clearfix');
  }

  protected function renderFirstAndPreviousLinks()
  {
    $html = '';
    
    if ($this->pager->getPage() !== $this->pager->getFirstPage())
    {
      if($first = $this->getOption('first'))
      {
        $html .= $this->helper->£('li.page.first', $this->renderLink($this->getFirstPage(), $first));
      }

      if($prev = $this->getOption('prev'))
      {
        $html .= $this->helper->£('li.page.prev', $this->renderLink($this->getPreviousPage(), $prev));
      }
    }

    return $html;
  }

  protected function renderPageLinks()
  {
    $links = array();
    
    foreach($this->getLinks($this->getOption('nb_links')) as $page)
    {
      // current page
      if($page === $this->pager->getPage())
      {
        $links[] = $this->helper->£('li.page.'.$this->getOption('current_class'), $this->helper->£('span.link', $page));
      }
      else
      {
        $links[] = $this->helper->£('li.page', $this->renderLink($page, $page));
      }
    }

    return join($this->getOption('separator') ? $this->helper->£('li.separator', $this->getOption('separator')) : '', $links);
  }

  protected function renderNextAndLastLinks()
  {
    $html = '';
    
    if ($this->pager->getPage() != $this->pager->getLastPage())
    {
      if($next = $this->getOption('next'))
      {
        $html .= $this->helper->£('li.page.next', $this->renderLink($this->getNextPage(), $next));
      }
      if($last = $this->getOption('last'))
      {
        $html .= $this->helper->£('li.page.last', $this->renderLink($this->getLastPage(), $last));
      }
    }

    return $html;
  }

  protected function closePager()
  {
    return '</ul></div>';
  }

  protected function renderLink($page, $text)
  {
    $link = $this->helper->£link($this->getBaseHref())->param('page', $page)->text($text);

    if($this->getOption('ajax'))
    {
      $link->json(array('href' => $this->helper->£link('+/dmWidget/render')
      ->param('page', $page)
      ->param('widget_id', $this->getOption('widget_id'))
      ->param('page_id', ($page = $this->context->getPage()) ? $page->id : null)
      ->getHref()));
    }

    return $link;
  }

  /*
   * Proxy to sfPager
   */
  public function __call($method, $arguments)
  {
    if(method_exists($this->pager, $method))
    {
      $return = call_user_func_array(array($this->pager, $method), $arguments);

      return $return === $this->pager ? $this : $return;
    }
    else
    {
      throw new dmException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }
  }

  /*
   * Interface implementations
   */
  /**
   * Returns the current result.
   *
   * @see Iterator
   */
  public function current()
  {
    return $this->pager->current();
  }

  /**
   * Returns the current key.
   *
   * @see Iterator
   */
  public function key()
  {
    return $this->pager->key();
  }

  /**
   * Advances the internal pointer and returns the current result.
   *
   * @see Iterator
   */
  public function next()
  {
    return $this->pager->next();
  }

  /**
   * Resets the internal pointer and returns the current result.
   *
   * @see Iterator
   */
  public function rewind()
  {
    return $this->pager->rewind();
  }

  /**
   * Returns true if pointer is within bounds.
   *
   * @see Iterator
   */
  public function valid()
  {
    return $this->pager->valid();
  }

  /**
   * Returns the total number of results.
   *
   * @see Countable
   */
  public function count()
  {
    return $this->pager->count();
  }
}