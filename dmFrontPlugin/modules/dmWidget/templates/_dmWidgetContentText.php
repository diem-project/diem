<?php

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