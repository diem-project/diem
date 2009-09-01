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
    $this->embedForm('<?php echo $mediaRelation['local'].'_form' ?>', $this->createMediaFormFor<?php echo dmString::camelize($mediaRelation['local']); ?>());
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
      $this->mergeForm($this->createCurrentI18nForm());
    }

    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->modelName) ?>[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

<?php foreach($this->getMediaRelations() as $mediaRelation): ?>
  /*
   * Create Media form for <?php echo $mediaRelation['local']."\n"; ?>
   */
  protected function createMediaFormFor<?php echo dmString::camelize($mediaRelation['local']); ?>()
  {
    return DmMediaForRecordForm::factory($this->object, '<?php echo $mediaRelation['local'] ?>', '<?php echo $mediaRelation['alias'] ?>', $this->validatorSchema['<?php echo $mediaRelation['local']; ?>']->getOption('required'));
  }
<?php endforeach; ?>

  protected function doBind(array $values)
  {
<?php foreach($this->getMediaRelations() as $mediaRelation): ?>
    $values = $this->filterValuesByEmbeddedMediaForm($values, '<?php echo $mediaRelation['local'] ?>');
<?php endforeach; ?>
    parent::doBind($values);
  }
  
  public function processValues($values = null)
  {
    $values = parent::processValues($values);
<?php foreach($this->getMediaRelations() as $mediaRelation): ?>
    $values = $this->processValuesForEmbeddedMediaForm($values, '<?php echo $mediaRelation['local'] ?>');
<?php endforeach; ?>
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
<?php foreach($this->getMediaRelations() as $mediaRelation): ?>
		$this->doUpdateObjectForEmbeddedMediaForm($values, '<?php echo $mediaRelation['local'] ?>', '<?php echo $mediaRelation['alias'] ?>');
<?php endforeach; ?>
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