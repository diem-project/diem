<?php

class dmWidgetContentTextView extends dmWidgetContentMediaView
{

  public function configure()
  {
    parent::configure();

    $this->removeRequiredVar(array('mediaId', 'method'));
  }

  public function getViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
    
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
  
  protected function doRender(array $vars)
  {
    extract($vars);
    
    $html = dmHelper::£o('div.dm_text.text_'.$style);

    if ($title && $titlePosition == 'outside')
    {
      $html .= dmHelper::£('h2.text_title.outside', $title);
    }

    $html .= dmHelper::£o('div.text_content.clearfix');
  
      if ($media && $mediaPosition != 'bottom')
      {
        $html .= dmHelper::£('div.text_image'.$mediaClass, $media);
      }
    
      if ($title && $titlePosition == 'inside')
      {
        $html .= dmHelper::£('h2.text_title.inside', $title);
      }
    
      $html .= dmHelper::£('text_markdown', dmContext::getInstance()->get('markdown')->toHtml($text));
    
      if ($media && $mediaPosition == 'bottom')
      {
        $html .= dmHelper::£('div.text_image'.$mediaClass, $media);
      }
  
    $html .= dmHelper::£c('div');
    
    $html .= dmHelper::£c('div');
    
    return $html;
  }
  
  public function toIndexableString(array $vars)
  {
    return implode(' ', $vars['title'], $vars['text'], $vars['legend']);
  }
}