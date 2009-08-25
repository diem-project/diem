<?php

require_once(dirname(__FILE__).'/vendor/markdown.php');

class dmMarkdown extends MarkdownExtra_Parser
{

	protected static $instance;

	public static function get($text)
	{
		if(is_null(self::$instance))
		{
			self::$instance = new self;
		}

		return trim(self::$instance->transform($text), "\n");
	}
	
	public static function toText($text)
	{
		return strip_tags(self::get($text));
	}

	public function transform($text)
	{
		$text = $this->preTransform($text);

		$text = parent::transform($text);

		$text = $this->postTransform($text);

		return $text;
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

    return $text;
  }

  protected function cleanText($text)
  {
    return strtr($text, array(
        "\r"      => ""      // réparation des sauts de ligne mac/windows
      , "\t"      => "  "    // tabs -> double espace
      , "&#8217;" => "'"     // apostrophe
      , '“'       => '&lquot;'
      , '”'       => '&rquot;'
      , '®'       => '&reg;'
      , '‘'       => '&lsquo;'
      , '’'       => '&rsquo;'
      , '�'       => ' '
    ));
  }
}