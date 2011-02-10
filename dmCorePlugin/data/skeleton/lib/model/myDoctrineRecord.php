<?php

abstract class myDoctrineRecord extends dmDoctrineRecord
{
	public function preSave($event)
	{
		die(dirname(__FILE__));
		parent::preSave($event);
		$this->notify('pre-save');
	}
}