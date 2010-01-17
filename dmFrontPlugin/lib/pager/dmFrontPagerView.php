<?php

class dmFrontPagerView extends dmConfigurable implements Iterator, Countable
{
  protected
  $pager,
  $context,
  $helper,
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
      'uri'               => $this->context->getPage()
      ? $this->helper->£link($this->context->getPage())->getAbsoluteHref()
      : $this->context->getRequest()->getUri()
    );
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
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

    $this->setOption('uri', preg_replace('|/page/([0-9]+)|', '?page=$1', $this->getOption('uri')));
    
    $hash = md5(var_export($this->getOptions(), true));

    if (isset($this->navigationCache[$hash]))
    {
      return $this->navigationCache[$hash];
    }

    $html =
    $this->openPager().
    $this->renderFirstAndPreviousLinks().
    $this->renderPageLinks().
    $this->renderNextAndLastLinks().
    $this->closePager();

    $html = preg_replace('|\?page=([0-9]+)|', '/page/$1', $html);

    $this->navigationCache[$hash] = $html;

    return $html;
  }

  protected function openPager()
  {
    return
    $this->helper->£o('div', array('class' => dmArray::toHtmlCssClasses(array('pager', $this->getOption('class'))))).
    $this->helper->£o('ul.clearfix');
  }

  protected function renderFirstAndPreviousLinks()
  {
    $html = '';
    
    if (1 !== $this->pager->getPage())
    {
      if($first = $this->getOption('first'))
      {
        $html .= $this->helper->£('li.page.first',
          $this->helper->£link($this->getOption('uri'))
          ->param('page', $this->getFirstPage())
          ->text($first)
        );
      }

      if($prev = $this->getOption('prev'))
      {
        $html .= $this->helper->£('li.page.prev',
          $this->helper->£link($this->getOption('uri'))
          ->param('page', $this->getPreviousPage())
          ->text($prev)
        );
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
        $links[] = $this->helper->£('li.page.'.$this->getOption('current_class'),
          $this->helper->£('span.link', $page)
        );
      }
      else
      {
        $links[] = $this->helper->£('li.page',
          $this->helper->£link($this->getOption('uri'))->param('page', $page)->text($page)
        );
      }
    }

    return join($this->getOption('separator') ? $this->helper->£('li.separator', $this->getOption('separator')) : '', $links);
  }

  protected function renderNextAndLastLinks()
  {
    $html = '';
    
    if ($this->pager->getPage() != $this->getCurrentMaxLink())
    {
      if($next = $this->getOption('next'))
      {
        $html .= $this->helper->£('li.page.next',
          $this->helper->£link($this->getOption('uri'))->param('page', $this->getNextPage())->text($next)
        );
      }
      if($last = $this->getOption('last'))
      {
        $html .= $this->helper->£('li.page.last',
          $this->helper->£link($this->getOption('uri'))->param('page', $this->getLastPage())->text($last)
        );
      }
    }

    return $html;
  }

  protected function closePager()
  {
    return '</ul></div>';
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