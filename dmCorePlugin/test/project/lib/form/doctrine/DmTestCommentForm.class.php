<?php

/**
 * DmTestComment form.
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DmTestCommentForm extends BaseDmTestCommentForm
{
  public function configure()
  {
    $this->validatorSchema['body']->setOption('required', true);
  }
}
