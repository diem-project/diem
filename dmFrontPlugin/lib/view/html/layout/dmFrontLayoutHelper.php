<?php

class dmFrontLayoutHelper extends dmCoreLayoutHelper
{
  protected
    $page;

  protected function initialize()
  {
    parent::initialize();

    $this->setPage($this->serviceContainer->getParameter('context.page'));
  }
    
  public function setPage(DmPage $page)
  {
    $this->page = $page;
  }
  
  public function renderHead()
  {
    return
    $this->renderHttpMetas().
    $this->renderMetas().
    $this->renderStylesheets().
    $this->renderBrowserStylesheets().
    $this->renderFavicon().
    $this->renderIeHtml5Fix();
  }
  
  public function renderBrowserStylesheets()
  {
    $html = '';
    $theme = $this->serviceContainer->getParameter('user.theme');

    // search in theme_dir/css/browser/ieX.css
    if (is_dir($theme->getFullPath('css/browser')))
    {
      foreach(array(6, 7, 8) as $ieVersion)
      {
        if (file_exists($theme->getFullPath('css/browser/msie'.$ieVersion.'.css')))
        {
          $html .= "\n".sprintf('<!--[if IE %d]><link href="%s" rel="stylesheet" type="text/css" /><![endif]-->',
            $ieVersion,
            $theme->getWebPath('css/browser/msie'.$ieVersion.'.css')
          );
        }
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
    $bodyClass = dmArray::toHtmlCssClasses(array(
      $this->page->get('module').'_'.$this->page->get('action'),
      $this->page->getPageView()->get('Layout')->get('css_class')
    ));
    
    return '<body class="'.$bodyClass.'">';
  }

  protected function getMetas()
  {
    $metas = array(
      'description'  => $this->page->get('description'),
      'language'     => $this->serviceContainer->getParameter('user.culture')
    );
    
    if (sfConfig::get('dm_seo_use_keywords') && $keywords = $this->page->get('keywords'))
    {
      $metas['keywords'] = $keywords;
    }
    
    if (!dmConfig::get('site_indexable') || !$this->page->get('is_indexable'))
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
    $metaHtml = "\n".'<title>'.dmConfig::get('title_prefix').$this->page->get('title').dmConfig::get('title_suffix').'</title>';
    
    foreach($this->getMetas() as $key => $value)
    {
      $metaHtml .= "\n".'<meta name="'.$key.'" content="'.$value.'" />';
    }

    return $metaHtml;
  }
  
  
  public function renderEditBars()
  {
    $user = $this->serviceContainer->getService('user');
    
    if (!$user->can('admin'))
    {
      return '';
    }
    
    $helper = $this->serviceContainer->getService('helper');
    
    $cacheKey = sfConfig::get('sf_cache') ? $user->getCredentialsHash() : null;
    
    $html = '';
    
    if (sfConfig::get('dm_pageBar_enabled', true) && $user->can('page_bar_front'))
    {
      $html .= $helper->renderPartial('dmInterface', 'pageBar', array('cacheKey' => $cacheKey));
    }
    
    if (sfConfig::get('dm_mediaBar_enabled', true) && $user->can('media_bar_front'))
    {
      $html .= $helper->renderPartial('dmInterface', 'mediaBar', array('cacheKey' => $cacheKey));
    }
    
    if ($user->can('tool_bar_front'))
    {
      $html .= $helper->renderComponent('dmInterface', 'toolBar', array('cacheKey' => $cacheKey));
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
    $user = $this->serviceContainer->getService('user');
    
    if (dmConfig::get('ga_key') && !$user->can('admin') && !dmOs::isLocalhost())
    {
      return str_replace("\n", ' ', '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("'.dmConfig::get('ga_key').'");
pageTracker._trackPageview();
} catch(err) {}</script>');
    }
    
    return '';
  }
}
