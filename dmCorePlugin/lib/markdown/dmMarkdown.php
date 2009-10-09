<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{
  
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
    $text = preg_replace("|^\.$|ium", "<br />", $text);

    // add two spaces before every line end to allow new lines
    $text = str_replace("\n", "  \n", $text);

    return $text;
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
        "&#8217;" => "'"     // apostrophe
      , '“'       => '&lquot;'
      , '”'       => '&rquot;'
      , '®'       => '&reg;'
      , '‘'       => '&lsquo;'
      , '’'       => '&rsquo;'
      , '�'       => ' '
    ));
  }
}