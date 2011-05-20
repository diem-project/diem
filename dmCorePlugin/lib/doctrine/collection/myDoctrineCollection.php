<?php

class myDoctrineCollection extends dmDoctrineCollection
{
	public function saveGet($conn = null)
	{
		$this->save($conn);
		return $this;
	}
}