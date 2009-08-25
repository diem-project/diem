<?php

class dmAdminHtmlFilter extends dmHtmlFilter
{

  public function execute($filterChain)
  {
    $response = $this->getContext()->getResponse();
    $request = $this->getContext()->getRequest();

    $filterChain->execute();

    $html = $response->getContent();

    if (dmContext::getInstance()->isHtmlForHuman())
    {
      $html = $response->getContent();

      if (strpos($html, "</h1>")) // le title prend la valeur du h1
      {
        preg_match(
          '|<h1[^>]*>(.*)</h1>|iuUx',
          $html,
          $matches
        );
        if (isset($matches[1]))
        {
        	$title = "Admin : ".strip_tags($matches[1])." - ".dmContext::getInstance()->getSite()->getName()." | Diem";
        	$html = preg_replace("|<title>[^<]*</title>|iuUx", "<title>$title</title>", $html);
        }
      }

      if ($request->useTidy())
      {
        $cleanCode = dmHtml::repair($html, array_merge(array(
		      'indent'        => sfConfig::get('dm_tidy_indent', true),
		      'indent-spaces' => sfConfig::get('dm_tidy_indent-spaces', 2),
		      'wrap'          => sfConfig::get('dm_tidy_wrap', 160),
		      'language'      => dm::getUser()->getCulture()
		    ), sfConfig::get('dm_tidy_params', array())));

		    if (sfConfig::get('dm_tidy_replace', true))
		    {
		    	$html = $cleanCode;
		    }

        if (dm::getUser()->can('tidy_output'))
	      {
	        $html = str_replace('__DM_TIDY_OUTPUT__', get_partial('dmUtil/tidyOutput', array('output' => sfConfig::get("dm_tidy_output"))), $html);
	      }
      }

      $response->setContent($html);
    }
  }

}