<?php

/*
 * Deprecated
 * Use dmSitemapMenu instead
 */
class dmFrontHtmlSitemap
{
  protected
  $helper,
  $culture;

  public function __construct(dmHelper $helper, $culture)
  {
    $this->helper   = $helper;
    $this->culture  = $culture;
  }

  public function render()
  {
    return $this->renderPage($this->getTree());
  }

  protected function renderPage(DmPage $page)
  {
    $html = $this->helper->link($page);
    
    if (count($children = $page->get('__children')))
    {
      $html .= '<ul>'."\n";
      
      foreach($children as $child)
      {
        $html .= '<li>'.$this->renderPage($child).'</li>'."\n";
      }
      
      $html .= '</ul>'."\n";
    }
    
    return $html;
  }
  
  protected function getTree()
  {
    return $this->getTreeQuery()->execute(array(), Doctrine_Core::HYDRATE_RECORD_HIERARCHY)->get(0);
  }

  protected function getTreeQuery()
  {
    return dmDb::query('DmPage p')
    ->withI18n($this->culture)
    ->where('pTranslation.is_active = ?', true)
    ->andWhere('pTranslation.is_secure = ?', false)
    ->andWhere('p.module != ? OR ( p.action != ? AND p.action != ? AND p.action != ?)', array('main', 'error404', 'search', 'signin'))
    ->orderBy('p.lft');
  }
  
  public function __toString()
  {
    try
    {
      return $this->render();
    }
    catch(Exception $e)
    {
      return $this->helper->link($e);
    }
  }
}