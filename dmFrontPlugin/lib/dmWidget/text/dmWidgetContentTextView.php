<?php

class dmWidgetContentTextView extends dmWidgetContentMediaView
{

  public function configure()
  {
    parent::configure();

    $this->removeRequiredVar(array('mediaId', 'method'));
  }

  public function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    if (!empty($vars['mediaId']))
    {
      $vars['mediaClass'] = '';
      $vars['mediaPosition'] = 'top';
    }

    $vars['titlePosition'] = 'outside';
    
    $vars['style'] = 'default';
    
    if(!isset($vars['title']))
    {
      $vars['title'] = null;
    }
    
    if(!isset($vars['text']))
    {
      $vars['text'] = null;
    }

    return $vars;
  }
  
  protected function doRender()
  {
    if ($this->isCachable() && $cache = $this->getCache())
    {
      return $cache;
    }
    
    extract($this->getViewVars());
    
    $html = dmHelper::£o('div.dm_text.text_'.$style.'.clearfix');

    if ($title && $titlePosition == 'outside')
    {
      $html .= dmHelper::£('h2.text_title.outside', $titleLink ? dmFrontLinkTag::build($titleLink)->text($title) : $title);
    }

    $html .= dmHelper::£o('div.text_content');
  
      if ($media && $mediaPosition != 'bottom')
      {
        $html .= dmHelper::£('div.text_image'.$mediaClass, $mediaLink ? dmFrontLinkTag::build($mediaLink)->text($mediaTag) : $mediaTag);
      }
    
      if ($title && $titlePosition == 'inside')
      {
        $html .= dmHelper::£('h2.text_title.inside', $titleLink ? dmFrontLinkTag::build($titleLink)->text($title) : $title);
      }
    
      $html .= dmHelper::£('div.markdown.text_markdown', $this->context->get('markdown')->toHtml($text));
    
      if ($media && $mediaPosition == 'bottom')
      {
        $html .= dmHelper::£('div.text_image'.$mediaClass, $mediaLink ? dmFrontLinkTag::build($mediaLink)->text($mediaTag) : $mediaTag);
      }
  
    $html .= dmHelper::£c('div');
    
    $html .= dmHelper::£c('div');
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  protected function doRenderForIndex()
  {
    $vars = $this->compiledVars();
    return implode(' ', array($vars['title'], $vars['text'], $vars['legend']));
  }
}