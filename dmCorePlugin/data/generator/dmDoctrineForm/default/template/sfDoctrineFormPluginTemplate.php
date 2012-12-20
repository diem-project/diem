[?php

/**
 * Plugin<?php echo $this->table->getOption('name') ?> form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 * @generator  <?php echo 'Diem ', constant('DIEM_VERSION'), "\n"?>
 * @gen-file   <?php echo __FILE__?>
 */
abstract class Plugin<?php echo $this->table->getOption('name') ?>Form extends Base<?php echo $this->table->getOption('name') ?>Form
{
  public function setup()
  {
    parent::setup();
    /*
     * Here, the plugin form code
     */
  }
}