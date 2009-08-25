<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInputFile represents an upload HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInputFile.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormDmInputFile extends sfWidgetFormInputFile
{
	/*
	 * Remove value attribute for html5 validation
	 */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name), $attributes));
  }
}
