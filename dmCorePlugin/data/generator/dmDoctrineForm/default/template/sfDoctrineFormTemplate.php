[?php

/**
 * <?php echo $this->table->getOption('name') ?> form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 * @generator  <?php echo 'Diem ', constant('DIEM_VERSION'), "\n"?>
 * @gen-file   <?php echo __FILE__?>
 */
class <?php echo $this->table->getOption('name') ?>Form extends Base<?php echo $this->table->getOption('name') ?>Form
{
<?php if ($parent = $this->getParentModel()): ?>
  /**
   * @see <?php echo $parent ?>Form
   */
<?php endif;?>
  public function configure()
  {
    parent::configure();
  }
}