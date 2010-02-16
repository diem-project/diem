  public function getCredentials($action)
  {
    if (0 === strpos($action, '_'))
    {
      $action = substr($action, 1);
    }

    return isset($this->configuration['credentials'][$action]) ? $this->configuration['credentials'][$action] : array();
  }

  public function getActionsDefault()
  {
    return <?php echo $this->asPhp(isset($this->config['actions']) ? $this->config['actions'] : array()) ?>;
<?php unset($this->config['actions']) ?>
  }

  public function getFormActions()
  {
    return <?php echo $this->asPhp(isset($this->config['form']['actions']) ? $this->config['form']['actions'] : array(
    '_list' => array('label' => 'Back to list'),
    '_save' => array('label' => 'Save'),
    '_save_and_add' => array('label' => 'Save and Add'),
    '_save_and_list' => array('label' => 'Save and Back to list'),
    '_save_and_next' => array('label' => 'Save and Next'),
    '_delete' => array('label' => 'Delete', 'title' => 'Delete this %1%'),
    '_add' => array('label' => 'Add', 'title' => 'Add a %1%'),
    '_view_page' => array('label' => 'Show', 'title' => 'View page on website'),
    '_history' => array('label' => 'History', 'title' => 'Revision history of %1%')
    )) ?>;
<?php unset($this->config['form']['actions']) ?>
  }

  public function getNewActions()
  {
    return <?php echo $this->asPhp(isset($this->config['new']['actions']) ? $this->config['new']['actions'] : array()) ?>;
<?php unset($this->config['new']['actions']) ?>
  }

  public function getEditActions()
  {
    return <?php echo $this->asPhp(isset($this->config['edit']['actions']) ? $this->config['edit']['actions'] : array()) ?>;
<?php unset($this->config['edit']['actions']) ?>
  }

  public function getListObjectActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['object_actions']) ? $this->config['list']['object_actions'] : array('_edit' => null, '_delete' => null)) ?>;
<?php unset($this->config['list']['object_actions']) ?>
  }

  public function getListActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['actions']) ? $this->config['list']['actions'] : array('_new' => array('label' => 'Add', 'title' => 'Add a %1%'))) ?>;
<?php unset($this->config['list']['actions']) ?>
  }

  public function getListBatchActions()
  {
    <?php
      $default = array('_delete' => null);
      if($this->getModule()->getTable()->hasField('is_active'))
      {
        $default['_activate'] = null;
        $default['_deactivate'] = null;
      }
    ?>
    return <?php echo $this->asPhp(isset($this->config['list']['batch_actions']) ? $this->config['list']['batch_actions'] : $default) ?>;
<?php unset($this->config['list']['batch_actions']) ?>
  }
