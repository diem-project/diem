<?php

class dmJsMinifier extends JsMinEnh
{
	
	public static function transform($script)
	{
		$minifier = new self($script);
		return $minifier->minify();
	}
	
}