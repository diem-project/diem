<?php

class myDoctrineCollection extends dmDoctrineCollection
{
	public function saveGet($conn = null)
	{
		$this->save($conn);
		return $this;
	}
	
	public function clear()
	{
		parent::clear();
		return $this;
	}
}
