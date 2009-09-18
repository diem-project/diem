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
    
    dm::loadHelpers('Dm');
    
    ob_start();
    
    echo £o('div.dm_text.text_'.$style);

      if ($title && $titlePosition == 'outside')
      {
        echo £('h2.text_title.outside', $title);
      }
      
      echo £o('div.text_content.clearfix');
    
        if ($media && $mediaPosition != 'bottom')
        {
          echo £('div.text_image'.$mediaClass, $media);
        }
      
        if ($title && $titlePosition == 'inside')
        {
          echo £('h2.text_title.inside', $title);
        }
      
        echo £('text_markdown', dmMarkdown::toHtml($text));
      
        if ($media && $mediaPosition == 'bottom')
        {
          echo £('div.text_image'.$mediaClass, $media);
        }
    
      echo £c('div');
    
    echo £c('div');
    
    return ob_get_clean();
  }
  
  public function toIndexableString(array $vars)
  {
    return implode(' ', $vars['title'], $vars['text'], $vars['legend']);
  }
}