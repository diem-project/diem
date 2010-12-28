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
  protected
  $dmModule;
  
  public function preExecute()
  {
    $this->configuration = new <?php echo $this->getModuleName() ?>GeneratorConfiguration();

    if ($this->isActionStrategicalySecurized() && !$this->userHasCredentials())
    {
      $this->forwardSecure();
    }

    $this->dispatcher->notify(new sfEvent($this, 'admin.pre_execute', array('configuration' => $this->configuration)));

    $this->helper = new <?php echo $this->getModuleName() ?>GeneratorHelper($this->getDmModule());
  }
  
  public function getDmModule()
  {
    if (null !== $this->dmModule)
    {
      return $this->dmModule;
    }
    
    return $this->dmModule = $this->context->getModuleManager()->getModule('<?php echo $this->getModule()->getKey(); ?>');
  }
  
  protected function getSfModule()
  {
    return '<?php echo $this->getModuleName(); ?>';
  }

	protected function userHasCredentials()
	{
		return $this->getActionSecurizationStrategy($this->actionName)->userHasCredentials($this->getUser()->getUser());
	}

	protected function addRecordPermissionQuery($query)
	{
		if($this->context->getUser()->getUser()->get('is_super_admin')){
			return;
		}

		if($this->isActionStrategicalySecurized())
		{
			return $this->getActionSecurizationStrategy($this->actionName)->addPermissionCheckToQuery($query, $this->actionName, $this->moduleName);
		}
		return $query;
	}

	protected function isActionStrategicalySecurized()
	{
		return $this->getDmModule()->hasSecurityConfiguration($this->getApplication(), 'actions', $this->actionName);
	}

	protected function getActionSecurizationStrategy()
	{
		$actionSecurity = $this->getDmModule()->getSecurityConfiguration($this->getApplication(), 'actions', $this->actionName);
		return $this->context->getServiceContainer()->getService('module_security_manager')->getStrategy($actionSecurity['strategy'], 'actions', $this->dmModule, $this);
	}

	protected function getApplication()
	{
		return $this->context->getConfiguration()->getApplication();
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
