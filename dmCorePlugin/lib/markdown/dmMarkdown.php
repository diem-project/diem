<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{
  protected
  $headerIdStack,
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
      'auto_header_id' => true
    );
  }
  
  public function reset()
  {
    $this->headerIdStack = array(
      1 => null,
      2 => null,
      3 => null,
      4 => null,
      5 => null
    );
  }
  
  public function toHtml($text)
  {
    $this->reset();
    
    return $this->postTransform($this->transform($this->preTransform($text)));
  }
  
  public function toText($text)
  {
    return strip_tags($this->toHtml($text));
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
    
    return $html;
  }
  
  public function _doHeaders_callback_atx($matches)
  {
    $level = strlen($matches[1]);
    
    $attr  = $this->_doHeaders_attr($id =& $matches[3]);
    
    $text = $this->runSpanGamut($matches[2]);
    
    if ($this->options['auto_header_id'] && false === strpos($attr, 'id="'))
    {
      $id = '';
      
      if(1 !== $level && !empty($this->headerIdStack[$level-1]))
      {
        $id = $this->headerIdStack[$level-1].':';
      }
      
      $id .= dmString::slugify($text);
      
      if (!empty($id))
      {
        $attr = ' id="'.$id.'"';
        
        if ($level < 6)
        {
          $this->headerIdStack[$level] = $id;
        }
      }
    }
    
    $block = "<h$level$attr>".$text."</h$level>";
    
    return "\n" . $this->hashBlock($block) . "\n\n";
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
  public static function brutalToText($text)
  {
    // remove common formatting
    $text = str_replace(array('-', '*', '#'), '', $text);
    
    // remove links
    $text = preg_replace('#\[([^\]]*)\]\([^\)]*\)#um', '$1', $text);
    
    return $text;
  }
}