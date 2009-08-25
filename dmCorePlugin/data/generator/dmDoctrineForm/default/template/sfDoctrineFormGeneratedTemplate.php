[?php

/**
 * <?php echo $this->modelName ?> form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
class Base<?php echo $this->modelName ?>Form extends <?php echo $this->getFormClassToExtend() . "\n" ?>
{
  public function setup()
  {
    $this->setWidgets(array(
<?php foreach ($this->getColumns() as $column): ?>
      '<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getFieldName())) ?> => new <?php echo $this->getWidgetClassForColumn($column) ?>(<?php echo $this->getWidgetOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
      '<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($relation['alias']).'_list')) ?> => new sfWidgetFormDoctrineChoiceMany(array('model' => '<?php echo $relation['table']->getOption('name') ?>', 'expanded' => true)),
<?php endforeach; ?>
    ));

    $this->setValidators(array(
<?php foreach ($this->getColumns() as $column): ?>
      '<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getFieldName())) ?> => new <?php echo $this->getValidatorClassForColumn($column) ?>(<?php echo $this->getValidatorOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
      '<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($relation['alias']).'_list')) ?> => new sfValidatorDoctrineChoiceMany(array('model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)),
<?php endforeach; ?>
    ));
<?php foreach($this->getMediaRelations() as $mediaRelation): ?>

    /*
     * Embed Media form for <?php echo $mediaRelation['local']."\n"; ?>
     */
    $media = $this->object->getDmMediaByColumnName('<?php echo $mediaRelation['local']; ?>');
    if(!$media || $media->isNew())
    {
      $media = new DmMedia;
      $media->Folder = $this->object->getDmMediaFolder();
      $this->object->setDmMediaByColumnName('<?php echo $mediaRelation['local']; ?>', $media);
    }
    $mediaForm = new DmMediaForm($media);
    $mediaForm->getValidator('file')->setOption('required', $this->validatorSchema['<?php echo $mediaRelation['local']; ?>']->getOption('required'));
    $this->embedForm('<?php echo $mediaRelation['local'].'_form'; ?>', $mediaForm);
    unset($this['<?php echo $mediaRelation['local']; ?>']);
<?php endforeach; ?>

<?php if ($uniqueColumns = $this->getUniqueColumnNames()): ?>
    $this->validatorSchema->setPostValidator(
<?php if (count($uniqueColumns) > 1): ?>
      new sfValidatorAnd(array(
<?php foreach ($uniqueColumns as $uniqueColumn): ?>
        new sfValidatorDoctrineUnique(array('model' => '<?php echo $this->table->getOption('name') ?>', 'column' => array('<?php echo implode("', '", $uniqueColumn) ?>'))),
<?php endforeach; ?>
      ))
<?php else: ?>
      new sfValidatorDoctrineUnique(array('model' => '<?php echo $this->table->getOption('name') ?>', 'column' => array('<?php echo implode("', '", $uniqueColumns[0]) ?>')))
<?php endif; ?>
    );

<?php endif; ?>

    if ($this->isI18n())
    {
      $this->embedCurrentI18n();
    }

    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->modelName) ?>[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
<?php foreach($this->getMediaRelations() as $mediaRelation): $formName = $mediaRelation['local'].'_form'; $mediaField = $mediaRelation['local']; $alias = $mediaRelation['alias']; ?>
    /*
     * Process media <?php echo $alias ?> form
     */
    $current<?php echo $alias ?> = $this->embeddedForms['<?php echo $formName; ?>']->getObject();
    if ($current<?php echo $alias ?>->isNew() && !$taintedFiles['<?php echo $formName; ?>']['file']['size'] && !$this->embeddedForms['<?php echo $formName; ?>']->getValidator('file')->getOption('required'))
    {
      // remove the embedded media form if the file field was not provided
      unset($this->embeddedForms['<?php echo $formName; ?>'], $taintedValues['<?php echo $formName; ?>'], $taintedFiles['<?php echo $formName; ?>']);
      // remove the created media
      $this->object-><?php echo $alias ?> = null;
      // pass the media form validations
      $this->validatorSchema['<?php echo $formName; ?>'] = new sfValidatorPass();
    }

    /*
     * We have a new file for an existing media.
     * Let's create a new media with the file,
     * and assign it legend, author and license
     * form the old media
     */
    elseif (!$current<?php echo $alias ?>->isNew() && $taintedFiles['<?php echo $formName; ?>']['file']['size'])
    {
      $taintedValues['<?php echo $formName; ?>']['id'] = null;
      $new<?php echo $alias ?> = dmDb::create('DmMedia', array(
        'legend'     => $current<?php echo $alias ?>->legend,
        'author'     => $current<?php echo $alias ?>->author,
        'license'    => $current<?php echo $alias ?>->license
      ));
      $new<?php echo $alias ?>->Folder = $this->object->getDmMediaFolder();

      $this->embeddedForms['<?php echo $formName; ?>']->setObject($new<?php echo $alias ?>);

      $this->object-><?php echo $alias ?> = $new<?php echo $alias ?>;
    }

    /*
     * We have no new file for an existing media.
     * Remove file validator
     */
    elseif (!$current<?php echo $alias ?>->isNew() && !$taintedFiles['<?php echo $formName; ?>']['file']['size'])
    {
      $this->validatorSchema['<?php echo $formName; ?>'] = new sfValidatorPass();
    }
<?php endforeach; ?>
    // call parent bind method
    parent::bind($taintedValues, $taintedFiles);
  }

  public function getModelName()
  {
    return '<?php echo $this->modelName ?>';
  }

<?php if ($this->getManyToManyRelations()): ?>
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
    if (isset($this->widgetSchema['<?php echo $this->underscore($relation['alias']) ?>_list']))
    {
      $this->setDefault('<?php echo $this->underscore($relation['alias']) ?>_list', $this->object-><?php echo $relation['alias']; ?>->getPrimaryKeys());
    }

<?php endforeach; ?>
  }

  protected function doSave($con = null)
  {
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
    $this->save<?php echo $relation['alias'] ?>List($con);
<?php endforeach; ?>

    parent::doSave($con);
  }

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
  public function save<?php echo $relation['alias'] ?>List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['<?php echo $this->underscore($relation['alias']) ?>_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $existing = $this->object-><?php echo $relation['alias']; ?>->getPrimaryKeys();
    $values = $this->getValue('<?php echo $this->underscore($relation['alias']) ?>_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('<?php echo $relation['alias'] ?>', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('<?php echo $relation['alias'] ?>', array_values($link));
    }
  }

<?php endforeach; ?>
<?php endif; ?>
}