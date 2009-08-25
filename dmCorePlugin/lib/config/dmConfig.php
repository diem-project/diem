<?php

class dmConfig
{

	public static function isCli()
	{
    return !isset($_SERVER['HTTP_HOST']);
	}

}