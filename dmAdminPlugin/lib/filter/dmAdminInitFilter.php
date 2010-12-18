<?php

class dmAdminInitFilter extends dmInitFilter
{

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    if($this->request->getParameter('dm_embed'))
    {
      sfConfig::set('dm_admin_embedded', true);
      sfConfig::set('dm_toolBar_enabled', false);
    }
    
    $this->saveApplicationUrl();

    $this->loadAssetConfig();

    if(sfConfig::get('dm_admin_embedded'))
    {
      $this->response->addStylesheet('admin.embed', 'last');

      $this->getContext()->getEventDispatcher()->connect('admin.save_object', array($this, 'listenToAdminSaveObjectWhenEmbedded'));
    }

    $this->updateLock();

    $filterChain->execute();

    if(sfConfig::get('dm_admin_embedded'))
    {
      $this->response->addStylesheet('admin.embed');
    }
    else
    {
      // If response has no title, generate one with the H1
      if ($this->response->isHtmlForHuman() && !$this->response->getTitle())
      {
        preg_match('|<h1[^>]*>(.*)</h1>|iuUx', $this->response->getContent(), $matches);

        if (isset($matches[1]))
        {
          $title = 'Admin : '.strip_tags($matches[1]).' - '.dmConfig::get('site_name');

          $this->response->setContent(str_replace('<title></title>', '<title>'.$title.'</title>', $this->response->getContent()));
        }
      }
    }
  }

  public function listenToAdminSaveObjectWhenEmbedded(sfEvent $e)
  {
    $this->getContext()->getEventDispatcher()->notify(new sfEvent($this, 'dm.controller.redirect'));
    
    print '<script type="text/javascript">
parent.document.getElementById("cboxClose").setAttribute("rel", "dm_close");
</script>';
    die;
  }

}