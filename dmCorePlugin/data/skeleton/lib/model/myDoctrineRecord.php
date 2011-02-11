<?php

abstract class myDoctrineRecord extends dmDoctrineRecord
{
	public function preSave($event)
	{
		parent::preSave($event);
		$this->notify('pre-save');
	}
}
