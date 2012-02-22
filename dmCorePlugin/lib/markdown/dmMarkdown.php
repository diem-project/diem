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
    $this->options = array_merge($this->getDefaultOptions(), $options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'auto_header_id' => true
    );
  }
  
  public function getOptions()
  {
    return $this->options;
  }
  
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;

    return $this;
  }
  
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name]) ? $this->options[$name] : $default;
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
    
    return $text;
  }
  
  protected function postTransform($html)
  {
    // remove first and last line feed
    $html = trim($html, "\n");
    
    // add the "dm_first_p" css class to the first p
    $html = dmString::str_replace_once('<p>', '<p class="dm_first_p">', $html);
    
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
  
  
  /**
   * Link system replacement
   */

  public function doAnchors($text)
  {
    #
    # Turn Markdown link shortcuts into XHTML <a> tags.
    #
    if ($this->in_anchor) return $text;
    $this->in_anchor = true;
    
    #
    # Next, inline-style links: [link text](url "optional title")
    #
    $text = preg_replace_callback('{
      (        # wrap whole match in $1
        \[
        ('.$this->nested_brackets_re.')  # link text = $2
        \]
        \(      # literal paren
        [ ]*
        (?:
          <(\S*)>  # href = $3
        |
          ('.$this->nested_url_parenthesis_re.')  # href = $4
        )
        [ ]*
        (      # $5
          ([\'"])  # quote char = $6
          (.*?)    # Title = $7
          \6    # matching quote
          [ ]*  # ignore any spaces/tabs between closing quote and )
        )?      # title is optional
        (           # $8
          (.*?)     # attrs = $9
          [ ]*
        )?          # attrs are optional
        \)
      )
      }xs',
      array(&$this, '_DoAnchors_inline_callback'), $text);

    $this->in_anchor = false;
    return $text;
  }
  
  public function _doAnchors_inline_callback($matches)
  {
    $text   = $this->runSpanGamut($matches[2]);
    $url    = $matches[3] == '' ? $matches[4] : $matches[3];
    $title  = $matches[7];
    $attrs  = $matches[9];
    
    if (false !== strpos($url, '#'))
    {
      $anchor = preg_replace('{.*\#([\w\d\-\:]+).*}i', '$1', $url);
      
      $url    = dmString::str_replace_once('#'.$anchor, '', $url);
    }
    else
    {
      $anchor = null;
    }
    
    if (strlen($url) == 0 && $anchor)
    {
      $link = $this->helper->link('#'.$anchor)->text($text);
    }
    else
    {
      $link = $this->helper->link($url)->text($text);
    
      if ($anchor)
      {
        $link->anchor($anchor);
      }
    }
    
    if ($title)
    {
      $link->title($title);
    }
    
    if ($attrs)
    {
      $link->set($attrs);
    }

    return $this->hashPart($link->render());
  }
  
  /**
   * Image system replacement
   */
  
  public function doImages($text)
  {
    #
    # Turn Markdown image shortcuts into <img> tags.
    #
    $text = preg_replace_callback('{
      (        # wrap whole match in $1
        !\[
        ('.$this->nested_brackets_re.')    # alt text = $2
        \]
        \s?      # One optional whitespace character
        \(      # literal paren
        [ ]*
        (?:
          <(\S*)>  # src url = $3
        |
          ('.$this->nested_url_parenthesis_re.')  # src url = $4
        )
        [ ]*
        (           # $5
          (.*?)     # attrs = $6
          [ ]*
        )?          # attrs are optional
        \)
      )
      }xs',
      array(&$this, '_doImages_inline_callback'), $text);

    return $text;
  }
  
  public function _doImages_inline_callback($matches)
  {
    $alt   = $matches[2];
    $url   = $matches[3] == '' ? $matches[4] : $matches[3];
    $attrs = $matches[6];

    $tag = $this->helper->media($url);
    
    if ($alt)
    {
      $tag->alt($alt);
    }
    
    if($attrs)
    {
      if(strpos($attrs, ' '))
      {
        list($css, $size) = explode(' ', $attrs);
      }
      elseif(in_array($attrs{0}, array('#', '.')))
      {
        list($css, $size) = array($attrs, null);
      }
      else
      {
        list($css, $size) = array(null, $attrs);
      }
      
      if ($css)
      {
        $tag->set($css);
      }
      
      if($size)
      {
        if (false !== strpos($size, 'x'))
        {
          list($width, $height) = explode('x', $size);
        }
        else
        {
          $width = $height = $size;
        }
        
        if($width)
        {
          $tag->width($width);
        }
        if($height)
        {
          $tag->height($height);
        }
      }
    }
    
    return $this->hashPart($tag->render());
  }
  
  protected function cleanText($text)
  {
    return str_replace("\r\n", "\n", $text);
  }
  
  /**
   * Very fast function to translate markdown text to pure text without formatting
   * This function is less efficient than toText
   */
  public static function brutalToText($text)
  {
    // remove common formatting
    $text = str_replace(array('-', '*', '#'), '', $text);
    
    // remove links and images
    $text = preg_replace('#!?\[([^\]]*)\]\([^\)]*\)#um', '$1', $text);
    
    return $text;
  }
}