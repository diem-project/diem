<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{
  protected
  $helper;
  
  public function __construct(dmHelper $helper)
  {
    $this->helper = $helper;
    
    parent::MarkdownExtra_Parser();
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
      '#\[([^\]]*)\]\(page\:(\d+)\)#u',
      array($this, 'replaceInternalLinkCallback'),
      $text
    );
  }
  
  protected function replaceInternalLinkCallback(array $matches)
  {
    if ($page = dmDb::table('DmPage')->findOneByIdWithI18n($matches[2]))
    {
      $link = $this->helper->£link($page);
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

  protected function postTransform($text)
  {
    // remove first and last line feed
    $text = trim($text, "\n");
    
    // add the "first_p" css class to the first p
    $text = dmString::str_replace_once('<p>', '<p class="first_p">', $text);
    
    return $text;
  }

  protected function cleanText($text)
  {
    return strtr($text, array(
        "\r\n"    => "\n",
        "&#8217;" => "'"     // apostrophe
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