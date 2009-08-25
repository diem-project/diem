<?php

class dmCssMinifier
{

  // remove comments, tabs, spaces, newlines, etc.
	public static function transform($style)
	{
		return str_replace(
			array("\r\n", "\r", "\n", "\t", '  ', '    ', '    ', '     '),
	    ' ',
			preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $style)
		);
	}

}