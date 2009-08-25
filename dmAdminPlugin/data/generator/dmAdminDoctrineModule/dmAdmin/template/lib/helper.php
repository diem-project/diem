[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: helper.php 12482 2008-10-31 11:13:22Z fabien $
 */
class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorHelper extends sfModelGeneratorHelper
{

  protected function getModule()
  {
    return dmModuleManager::getModule("<?php echo $this->getModuleName() ?>");
  }

  public function linkToNew($params)
  {
    return link_to1(
    __($params['label']),
    array('sf_route' => $this->getUrlForAction('new')),
    array('class' => 'sf_admin_action_new s16 s16_add', 'title' => __('Add a').' '.strtolower($this->getModule()->getName()))
    );
  }

  public function linkToEdit($object, $params)
  {
    return '<li class="sf_admin_action_edit">'.link_to1(__($params['label']), array(
    'sf_route' => $this->getUrlForAction('edit'),
    'sf_subject' => $object
    ),
    array('class' => 's16block s16_edit')
    ).'</li>';
  }

  public function linkToDelete($object, $params)
  {
    if ($object->isNew())
    {
      return '';
    }

    return '<li class="sf_admin_action_delete">'.link_to1("<span class=\"s16 s16_delete\">".__($params['label'])."</span>", array(
    'sf_route' => $this->getUrlForAction('delete'),
    'sf_subject' => $object
    ),
    array(
    'method' => 'delete',
    'confirm' => false,
    'class' => 'button red confirm_me',
    'title' => __('Delete this element')
    )).'</li>';
  }

  public function linkToList($params)
  {
    return '<li class="sf_admin_action_list">'.link_to(__($params['label']), $this->getUrlForAction('list')).'</li>';
  }

  public function linkToSave($object, $params)
  {
    return '<li class="dm_save_buttons"><div class="sf_admin_action_save"><input class="green" type="submit" value="'.__($params['label']).'" /></div>';
  }

  public function linkToSaveAndAdd($object, $params)
  {
    return '<div class="dm_hidden_buttons"><div class="sf_admin_action_save_and_add"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_add" /></div>';
  }

  public function linkToSaveAndList($object, $params)
    {
    return '<div class="sf_admin_action_save_and_list"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_list" /></div>';
    }

  public function linkToSaveAndNext($object, $params)
  {
    return '<div class="sf_admin_action_save_and_next"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_next" /></div></div></li>';
  }

  public function getUrlForAction($action)
  {
    return 'list' == $action ? '<?php echo $this->params['route_prefix'] ?>' : '<?php echo $this->params['route_prefix'] ?>_'.$action;
  }
}
