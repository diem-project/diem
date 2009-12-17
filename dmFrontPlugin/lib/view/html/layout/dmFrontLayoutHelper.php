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
  
  public function renderBodyTag()
  {
    $bodyClass = dmArray::toHtmlCssClasses(array(
      $this->page->get('module').'_'.$this->page->get('action'),
      $this->page->getPageView()->getLayout()->get('css_class')
    ));
    
    return '<body class="'.$bodyClass.'">';
  }

  protected function getMetas()
  {
    $metas = array(
      'description'  => $this->page->get('description'),
      'language'     => $this->serviceContainer->getParameter('user.culture'),
      'title'        => dmConfig::get('title_prefix').$this->page->get('title').dmConfig::get('title_suffix')
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
      $metas['google-site-verification'] = dmConfig::get('gwt_key');
    }
    
    return $metas;
  }
  
  public function renderEditBars()
  {
    $user = $this->serviceContainer->getService('user');
    
    if (!$user->can('admin'))
    {
      return '';
    }
    
    $helper = $this->getHelper();
    
    $cacheKey = sfConfig::get('sf_cache') ? $user->getCacheHash() : null;
    
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
    if (($gaKey = dmConfig::get('ga_key')) && !$this->serviceContainer->getService('user')->can('admin') && !dmOs::isLocalhost())
    {
      return $this->getGoogleAnalyticsCode($gaKey);
    }
    
    return '';
  }
  
  protected function getGoogleAnalyticsCode($gaKey)
  {
    return "<script type=\"text/javascript\">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '".$gaKey."']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
})();
</script>";
  }
}
