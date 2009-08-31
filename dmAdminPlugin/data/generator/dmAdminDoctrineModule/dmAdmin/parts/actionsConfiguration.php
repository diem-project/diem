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
//    '_save_and_add' => array('label' => '& Add'),
//    '_save_and_list' => array('label' => '& List'),
//    '_save_and_next' => array('label' => '& Next'),
    '_add' => null,
    '_delete' => array('label' => 'Delete')
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
    return <?php echo $this->asPhp(isset($this->config['list']['actions']) ? $this->config['list']['actions'] : array('_new' => null)) ?>;
<?php unset($this->config['list']['actions']) ?>
  }

  public function getListBatchActions()
  {
    <?php
      $default = array('_delete' => null);
      if($this->getModule()->getTable()->hasField('is_active'))
      {
        $default['_activate'] = null;
        $default['_desactivate'] = null;
      }
    ?>
    return <?php echo $this->asPhp(isset($this->config['list']['batch_actions']) ? $this->config['list']['batch_actions'] : $default) ?>;
<?php unset($this->config['list']['batch_actions']) ?>
  }
