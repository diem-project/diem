<?php

require_once(realpath(dirname(__FILE__).'/vendor').'/JsMinEnh.php');

class dmJsMinifier extends JsMinEnh
{
	
	public static function transform($script)
	{
		$minifier = new self($script);
		return $minifier->minify();
	}
	
}