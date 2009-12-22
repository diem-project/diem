<?php
/**
 * Contact components
 * 
 * No redirection nor database manipulation ( insert, update, delete ) here
 */
class dmContactComponents extends myFrontModuleComponents
{

  public function executeForm()
  {
    $this->form = $this->forms['DmContact'];
  }


}
