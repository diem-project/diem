<?php

/**
 * Model generator field.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfModelGeneratorConfigurationField.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class dmModelGeneratorConfigurationField extends sfModelGeneratorConfigurationField
{
  public function isBig()
  {
    return $this->getConfig('is_big') || $this->isMarkdown();
  }

  public function isMarkdown()
  {
    return $this->getConfig('markdown');
  }
  
}