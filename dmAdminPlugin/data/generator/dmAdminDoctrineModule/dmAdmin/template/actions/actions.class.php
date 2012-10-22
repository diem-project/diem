[?php

require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorConfiguration.class.php');
require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorHelper.class.php');

/**
 * <?php echo $this->getModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 12493 2008-10-31 14:43:26Z fabien $
 */
abstract class <?php echo $this->getGeneratedModuleName() ?>Actions extends <?php echo $this->getActionsBaseClass() ?>
{
  /**
	 * @var dmModule
	 */
  protected $dmModule;
  
  protected $helperClass = '<?php echo $this->getModuleName() ?>GeneratorHelper';
  
  protected $configurationClass = '<?php echo $this->getModuleName() ?>GeneratorConfiguration';
  
  /**
   * @return string the module key
   */
  protected function getModuleKey()
  {
  	return '<?php echo $this->getModule()->getKey()?>';
  }
  
  /**
	 * @return string the symfony module name
	 */
  protected function getSfModule()
  {
    return '<?php echo $this->getModuleName(); ?>';
  }

<?php include dirname(__FILE__).'/../../parts/exportAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/indexAction.php' ?>

<?php if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filterAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/newAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/createAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/editAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/updateAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/deleteAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/processFormAction.php' ?>

<?php if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filtersAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/paginationAction.php' ?>
}
