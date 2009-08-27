<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{

	protected static $instance;

	public static function toHtml($text)
	{
		if(is_null(self::$instance))
		{
			self::$instance = new self;
		}

		return trim(self::$instance->transform($text), "\n");
	}
	
	public static function toText($text)
	{
		return strip_tags(self::toHtml($text));
	}

	public function transform($text)
	{
		return self::postTransform(parent::transform(self::preTransform($text)));
	}

	protected static function preTransform($text)
	{
		// clean text
		$text = self::cleanText($text);

    // replace lines with only a dot by a <br />
    $text = preg_replace("|^\.$|ium", "<br />", $text);

		// add two spaces before every line end to allow new lines
		$text = str_replace("\n", "  \n", $text);

		return $text;
	}

  protected static function postTransform($text)
  {

    return $text;
  }

  protected static function cleanText($text)
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