[?php

/**
 * <?php echo $this->modelName ?> form base class.
 *
 * @method <?php echo $this->modelName ?> getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id$
 */
abstract class Base<?php echo $this->modelName ?>Form extends <?php echo $this->getFormClassToExtend() . "\n" ?>
{
  public function setup()
  {
    $this->setWidgets(array(
<?php foreach($this->getColumnAggregationKeyFields() as $column):?>
			'<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($column->getFieldName())) ?> => new sfWidgetFormChoice(array('choices' => <?php echo $this->arrayExport($this->getSubClassesChoices());?>)),
<?php endforeach;?>

<?php foreach ($this->getColumns(true, true) as $column): ?>
      '<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($column->getFieldName())) ?> => new <?php echo $this->getWidgetClassForColumn($column) ?>(<?php echo $this->getWidgetOptionsForColumn($column) ?>),
<?php endforeach; ?>

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
  <?php if ('DmMedia' === $relation->getClass()) continue; ?>
      '<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['alias']).'_list')) ?> => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'expanded' => true)),
<?php endforeach; ?>

<?php foreach($this->getOneToManyRelations() as $relation):?>
			<?php if($relation['alias'] === 'Translation') continue;?>
			'<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['alias']).'_list')) ?> => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'expanded' => true)),
<?php endforeach;?>

<?php foreach($this->getOneToOneRelations() as $relation):?>
			'<?php echo $this->underscore($relation['local']) ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['local']))) ?> => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => '<?php echo $relation['table']->getOption('name')?>', 'expanded' => false)),
<?php endforeach;?>

    ));

    $this->setValidators(array(
<?php foreach($this->getColumnAggregationKeyFields() as $column):?>
			'<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($column->getFieldName())) ?> => new sfValidatorChoice(array('choices' => <?php echo $this->arrayExport($this->getSubClassesChoicesValidator());?>, 'required' => true)),
<?php endforeach;?>

<?php foreach ($this->getColumns(true, true) as $column): ?>
      '<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($column->getFieldName())) ?> => new <?php echo $this->getValidatorClassForColumn($column) ?>(<?php echo $this->getValidatorOptionsForColumn($column) ?>),
<?php endforeach; ?>

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
  <?php if ('DmMedia' === $relation->getClass()) continue; ?>
      '<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['alias']).'_list')) ?> => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)),
<?php endforeach; ?>

<?php foreach($this->getOneToManyRelations() as $relation):?>
			'<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['alias']).'_list')) ?> => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)),
<?php endforeach;?>

<?php foreach($this->getOneToOneRelations() as $relation):?>
			'<?php echo $this->underscore($relation['local']) ?>'<?php echo str_repeat(' ', $this->getNumberOfSpaces($this->underscore($relation['local']))) ?> => new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => '<?php echo $relation['table']->getOption('name')?>', 'required' => true)),
<?php endforeach;?>
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
<?php if ($this->table->hasI18n()): ?>
    if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

<?php endif; ?>
    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->modelName) ?>[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }

<?php foreach($this->getMediaRelations() as $mediaRelation): ?>
  /**
   * Creates a DmMediaForm instance for <?php echo $mediaRelation['local']."\n"; ?>
   *
   * @return DmMediaForm a form instance for the related media
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
  
  public function processValues($values)
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

    if (null === $con)
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