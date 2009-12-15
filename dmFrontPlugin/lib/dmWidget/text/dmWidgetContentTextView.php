<?php

class dmWidgetContentTextView extends dmWidgetContentImageView
{

  public function configure()
  {
    parent::configure();

    $this->removeRequiredVar(array('mediaId', 'method'));
    $this->addRequiredVar('titlePosition');
  }

  public function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    if (!empty($vars['mediaId']))
    {
      $vars['mediaClass'] = '';
      $vars['mediaPosition'] = 'top';
    }

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
    
    $helper = $this->context->getHelper();
    
    $html = $helper->£o('div.dm_text.text_'.$style.'.clearfix');

    if ($title && $titlePosition == 'outside')
    {
      $html .= $helper->£('h2.text_title.outside', $titleLink ? $this->context->getHelper()->£link($titleLink)->text($title) : $title);
    }

    $html .= $helper->£o('div.text_content');
  
      if ($media && $mediaPosition != 'bottom')
      {
        $html .= $helper->£('div.text_image'.$mediaClass, $mediaLink ? $this->context->getHelper()->£link($mediaLink)->text($mediaTag) : $mediaTag);
      }
    
      if ($title && $titlePosition == 'inside')
      {
        $html .= $helper->£('h2.text_title.inside', $titleLink ? $this->context->getHelper()->£link($titleLink)->text($title) : $title);
      }
    
      $html .= $helper->£('div.markdown.text_markdown', $this->context->get('markdown')->toHtml($text));
    
      if ($media && $mediaPosition == 'bottom')
      {
        $html .= $helper->£('div.text_image'.$mediaClass, $mediaLink ? $this->context->getHelper()->£link($mediaLink)->text($mediaTag) : $mediaTag);
      }
  
    $html .= $helper->£c('div');
    
    $html .= $helper->£c('div');
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  protected function doRenderForIndex()
  {
    return implode(' ', array($this->compiledVars['title'], $this->compiledVars['text'], $this->compiledVars['legend']));
  }
}