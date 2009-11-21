<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{
  protected
  $helper;
  
  public function __construct(dmHelper $helper, array $options = array())
  {
    $this->helper = $helper;
    
    parent::MarkdownExtra_Parser();
    
    $this->initialize($options);
  }
  
  public function initialize(array $options)
  {
    $this->options = array_merge($options, $this->getDefaultOptions());
  }
  
  public function getDefaultOptions()
  {
    return array(
      'h2_id' => '_%text%',
      'h3_id' => '__%text%',
      'h4_id' => false
    );
  }
  
  public function toText($text)
  {
    return strip_tags($this->toHtml($text));
  }
  
  public function toHtml($text)
  {
    return $this->postTransform($this->transform($this->preTransform($text)));
  }

  protected function preTransform($text)
  {
    // clean text
    $text = $this->cleanText($text);

    // replace lines with only a dot by a <br />
    $text = preg_replace('|^\.$|ium', '<br />', $text);

    // add two spaces before every line end to allow new lines
    $text = str_replace("\n", "  \n", $text);

    $text = $this->replaceInternalLinks($text);
    
    return $text;
  }
  
  protected function replaceInternalLinks($text)
  {
    return preg_replace_callback(
      '#\[([^\]]*)\]\((page\:[^\)]+)\)#u',
      array($this, 'replaceInternalLinkCallback'),
      $text
    );
  }
  
  protected function replaceInternalLinkCallback(array $matches)
  {
    $source = $matches[2];
      
    if ($anchorPos = strpos($source, '#'))
    {
      $anchor = substr($source, $anchorPos+1);
      $source = substr($source, 0, $anchorPos);
    }
      
    if ($titlePos = strpos($source, ' '))
    {
      $title  = trim(substr($source, $titlePos+1), '"');
      $source = substr($source, 0, $titlePos);
    }
    
    if ($page = dmDb::table('DmPage')->findOneBySource($source))
    {
      $link = $this->helper->£link($page);
      
      if (isset($anchor))
      {
        $link->anchor($anchor);
      }
      if (isset($title))
      {
        $link->title($title);
      }
    }
    else
    {
      $link = $this->helper->£link('#');
    }
    
    if ($matches[1])
    {
      $link->text($matches[1]);
    }
    
    return $link->render();
  }

  protected function postTransform($html)
  {
    // remove first and last line feed
    $html = trim($html, "\n");
    
    // add the "first_p" css class to the first p
    $html = dmString::str_replace_once('<p>', '<p class="first_p">', $html);
    
    $html = $this->addHeaderIds($html);
    
    return $html;
  }
  
  protected function addHeaderIds($html)
  {
    foreach(array('h2', 'h3', 'h4') as $tag)
    {
      if (!$pattern = $this->options[$tag.'_id'] || !strpos($html, '<'.$tag.' />'))
      {
        continue;
      }
      
      $html = preg_replace_callback(
        '#<('.$tag.')[^>]*>(.*)</'.$tag.'>#uUx',
        array($this, 'addHeaderIdCallback'),
        $html
      );
    }
    
    return $html;
  }
  
  protected function addHeaderIdCallback(array $matches)
  {
    $tag = $matches[1];
    
    $text = str_replace('œ', 'oe', dmString::removeAccents($matches[2]));

    // strip all non word chars
    // replace all white space sections with a dash
    $text = preg_replace(array('/\W/', '/\s+/'), array(' ', '_'), $text);

    $text = trim($text, '_');
    
    $id = str_replace('%text%', $text, $this->options[$tag.'_id']);
    
    return str_replace('<'.$tag, '<'.$tag.' id="'.$id.'" ', $matches[0]);
  }

  protected function cleanText($text)
  {
    return strtr($text, array(
        "\r\n"    => "\n",
        "&#8217;" => "'"
      , '“'       => '&lquot;'
      , '”'       => '&rquot;'
      , '®'       => '&reg;'
      , '‘'       => '&lsquo;'
      , '’'       => '&rsquo;'
      , '�'       => ' '
    ));
  }
  
  
  /*
   * Very fast function to translate markdown text to pure text without formatting
   * This function is less efficient than toText
   */
  public function brutalToText($text)
  {
    // remove common formatting
    $text = str_replace(array('-', '*', '#'), '', $text);
    
    // remove links
    $text = preg_replace('#\[([^\]]*)\]\([^\)]*\)#u', '$1', $text);
    
    return $text;
  }
}