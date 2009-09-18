<?php

class dmFrontLayoutHelper extends dmCoreLayoutHelper
{
  protected
    $page;

  public function connect()
  {
    $this->dispatcher->connect('dm.context.change_page', array($this, 'listenToChangePageEvent'));
  }
  
  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangePageEvent(sfEvent $event)
  {
    $this->setPage($event['page']);
  }
  
  public function setPage(DmPage $page)
  {
    $this->page = $page;
  }
  
  public function renderBrowserStylesheets()
  {
    $html = '';

    // search in theme_dir/css/browser/ieX.css
    foreach(array(6, 7, 8) as $ieVersion)
    {
      if (file_exists($this->theme->getFullPath('css/browser/msie'.$ieVersion.'.css')))
      {
        $html .= "\n".sprintf('<!--[if IE %d]><link href="%s" rel="stylesheet" type="text/css" /><![endif]-->',
          $ieVersion,
          $this->theme->getWebPath('css/browser/msie'.$ieVersion.'.css')
        );
      }
    }

    return $html;
  }


  public function renderIeHtml5Fix()
  {
    return '<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->';
  }
  
  public function renderBodyTag()
  {
    printf('<body class="%s_%s">',
      $this->page->get('module'),
      $this->page->get('action')
    );
  }

  protected function getMetas()
  {
    $metas = array(
      'description'  => $this->page->get('description'),
      'language'     => $this->user->getCulture(),
      'generator'    => 'Diem '.dm::version()
    );
    
    if (sfConfig::get('dm_seo_use_keywords'))
    {
      $metas['keywords'] = $this->page->get('keywords');
    }
    
    if (!dmConfig::get('site_indexable'))
    {
      $metas['robots'] = 'noindex, nofollow';
    }
    
    if (dmConfig::get('gwt_key') && $this->page->getNode()->isRoot())
    {
      $metas['verify-v1'] = dmConfig::get('gwt_key');
    }
    
    return $metas;
  }
  
  public function renderMetas()
  {
    $metaHtml = array(sprintf('<title>%s</title>', $this->page->get('title')));
    
    foreach($this->getMetas() as $key => $value)
    {
      $metaHtml[] = sprintf('<meta name="%s" content="%s" />', $key, $value);
    }

    return implode(' ', $metaHtml);
  }
  
  
  public function renderEditBars()
  {
    if (!$this->user->can('admin'))
    {
      return '';
    }
    
    $html = '';
    
    if (sfConfig::get('dm_pageBar_enabled', true) && $this->user->can('page_bar_front'))
    {
      $html .= $this->helper->renderPartial('dmInterface', 'pageBar');
    }
    
    if (sfConfig::get('dm_mediaBar_enabled', true) && $this->user->can('media_bar_front'))
    {
      $html .= $this->helper->renderPartial('dmInterface', 'mediaBar');
    }
    
    if ($this->user->can('tool_bar_front'))
    {
      $html .= $this->helper->renderComponent('dmInterface', 'toolBar');
    }
    
    return $html;
  }

  public function getJavascriptConfig()
  {
    return array_merge(parent::getJavascriptConfig(), array(
      'page_id' => $this->page->get('id')
    ));
  }
  
  public function renderGoogleAnalytics()
  {
    if (dmConfig::get('ga_key') && !$this->user->can('admin') && !dmOs::isLocalhost())
    {
      return str_replace("\n", ' ', sprintf('<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("%s");
pageTracker._trackPageview();
} catch(err) {}</script>', dmConfig::get('ga_key')));
    }
    
    return '';
  }
}