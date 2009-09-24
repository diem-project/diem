<?php

class dmAdminHtmlFilter extends dmHtmlFilter
{

  public function execute($filterChain)
  {
    $filterChain->execute();

    if ($this->context->getResponse()->isHtmlForHuman())
    {
      $request = $this->context->getRequest();
      $response = $this->context->getResponse();
      $html = $response->getContent();
      
      // title is same as H1
      if (strpos($html, '</h1>'))
      {
        preg_match(
          '|<h1[^>]*>(.*)</h1>|iuUx',
          $html,
          $matches
        );
        if (isset($matches[1]))
        {
          $title = 'Admin : '.strip_tags($matches[1]).' - '.dmConfig::get('site_name').' | Diem';
          $html = preg_replace('|<title>[^<]*</title>|iuUx', '<title>'.$title.'</title>', $html);
        }
      }

      if ($request->useTidy())
      {
        $cleanCode = dmHtml::repair($html, array_merge(array(
          'indent'        => sfConfig::get('dm_tidy_indent', true),
          'indent-spaces' => sfConfig::get('dm_tidy_indent-spaces', 2),
          'wrap'          => sfConfig::get('dm_tidy_wrap', 160),
          'language'      => $this->context->getUser()->getCulture()
        ), sfConfig::get('dm_tidy_params', array())));

        if (sfConfig::get('dm_tidy_replace', true))
        {
          $html = $cleanCode;
        }

        if ($this->context->getUser()->can('tidy_output'))
        {
//          $html = str_replace('__DM_TIDY_OUTPUT__', get_partial('dmUtil/tidyOutput', array('output' => sfConfig::get('dm_tidy_output'))), $html);
        }
      }

      $response->setContent($html);
    }
  }

}