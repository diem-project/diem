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
    return '<li class="sf_admin_action_list">'.link_to1(__($params['label']), array('sf_route' => $this->getUrlForAction('list')), array('class' => 's16 s16_arrow_up_left')).'</li>';
  }

  public function linkToSave($object, $params)
  {
    return '<li class="sf_admin_action_save"><input class="green" type="submit" value="'.__($params['label']).'" /></li>';
  }

  public function linkToAdd($params)
  {
    return '<li class="sf_admin_action_add">'.$this->linkToNew($params).'</li>';
  }

  public function linkToSaveAndAdd($object, $params)
  {
    return '<li class="sf_admin_action_save_and_add"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_add" /></li>';
  }

  public function linkToSaveAndList($object, $params)
  {
    return '<li class="sf_admin_action_save_and_list"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_list" /></li>';
  }

  public function linkToSaveAndNext($object, $params)
  {
    return '<li class="sf_admin_action_save_and_next"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_next" /></li>';
  }

  public function getUrlForAction($action)
  {
    return 'list' == $action ? '<?php echo $this->params['route_prefix'] ?>' : '<?php echo $this->params['route_prefix'] ?>_'.$action;
  }
}
