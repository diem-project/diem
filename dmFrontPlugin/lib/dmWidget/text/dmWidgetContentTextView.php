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

    $viewVars = $this->getViewVars();
    extract($viewVars);
    
    $helper = $this->getHelper();
    
    $html = $helper->open('div.dm_text.text_'.$style.'.clearfix');

    if ($title && $titlePosition == 'outside')
    {
      $html .= $helper->tag('h2.text_title.outside', $titleLink ? $titleLinkTarget ? $helper->link($titleLink)->text($title)->target($titleLinkTarget) : $helper->link($titleLink)->text($title) : $title);
    }

    $html .= $helper->open('div.text_content');
  
      if ($media && $mediaPosition != 'bottom')
      {
        $html .= $helper->tag('div.text_image'.$mediaClass, $mediaLink ? $mediaLinkTarget ? $helper->link($mediaLink)->text($mediaTag)->target($mediaLinkTarget) :  $helper->link($mediaLink)->text($mediaTag) : $mediaTag);
      }
    
      if ($title && $titlePosition == 'inside')
      {
        $html .= $helper->tag('h2.text_title.inside', $titleLink ? $titleLinkTarget ? $helper->link($titleLink)->text($title)->target($titleLinkTarget) : $helper->link($titleLink)->text($title) : $title);
      }
    
      $html .= $helper->tag('div.markdown.text_markdown', $this->getService('markdown')->toHtml($text));
    
      if ($media && $mediaPosition == 'bottom')
      {
        $html .= $helper->tag('div.text_image'.$mediaClass, $mediaLink ? $mediaLinkTarget ? $helper->link($mediaLink)->text($mediaTag)->target($mediaLinkTarget) :  $helper->link($mediaLink)->text($mediaTag) : $mediaTag);
      }
  
    $html .= $helper->close('div');
    
    $html .= $helper->close('div');
    
    if ($this->isCachable())
    {
      $this->setCache($html);
    }
    
    return $html;
  }
  
  protected function doRenderForIndex()
  {
    $text = implode(' ', array(
      $this->compiledVars['title'],
      $this->compiledVars['text'],
      $this->compiledVars['legend']
    ));

    return $text;
  }
}