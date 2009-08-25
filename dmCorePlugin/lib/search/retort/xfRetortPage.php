<?php
/**
 * A retort for pages.
 */
class dmSearchRetortPage extends dmMicroCache implements xfRetort
{
	
  /**
   * @see xfRetort
   */
  public function can(xfDocumentHit $hit, $method, array $args = array())
  {
    return $method = 'getPage';
  }

  /**
   * @see xfRetort
   */
  public function respond(xfDocumentHit $hit, $method, array $args = array())
  {
  	$id = $hit->getDocument()->getField('id')->getValue();
  	
  	if ($this->hasCache($id))
  	{
  		return $this->getCache($id);
  	}
  	
  	return $this->setCache($id, dmDb::table('DmPage')->findOneByIdWithI18n($id));
  }
  
}