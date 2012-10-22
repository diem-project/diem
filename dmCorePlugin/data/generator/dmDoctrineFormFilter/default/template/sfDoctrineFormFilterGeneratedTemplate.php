[?php

/**
 * <?php echo $this->table->getOption('name') ?> filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class Base<?php echo $this->table->getOption('name') ?>FormFilter extends <?php echo $this->getFormClassToExtend() . "\n" ?>
{
  public function setup()
  {

<?php foreach($this->getColumnAggregationKeyFields() as $column):?>
		if($this->needsWidget('<?php echo $column->getFieldName()?>')){
			$this->setWidget('<?php echo $column->getFieldName()?>', new sfWidgetFormChoice(array('multiple' => true, 'choices' => <?php echo $this->arrayExport($this->getSubClassesChoices());?>)));
			$this->setValidator('<?php echo $column->getFieldName()?>', new sfValidatorChoice(array('multiple' => true, 'choices' => <?php echo $this->arrayExport($this->getSubClassesChoicesValidator());?>, 'required' => true)));
		}
<?php endforeach;?>

<?php foreach ($this->getColumns(true, true, true) as $column): ?>
		if($this->needsWidget('<?php echo $column->getFieldName()?>')){
			$this->setWidget('<?php echo $column->getFieldName() ?>', new <?php echo $this->getWidgetClassForColumn($column) ?>(<?php echo $this->getWidgetOptionsForColumn($column) ?>));
			$this->setValidator('<?php echo $column->getFieldName() ?>', <?php echo $this->getValidatorForColumn($column) ?>);
		}
<?php endforeach; ?>

<?php foreach ($this->getManyToManyRelations() as $relation): ?><?php if ('DmMedia' === $relation->getClass()) continue; ?>
		if($this->needsWidget('<?php echo $this->underscore($relation['alias']) ?>_list')){
			$this->setWidget('<?php echo $this->underscore($relation['alias']) ?>_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'expanded' => true)));
			$this->setValidator('<?php echo $this->underscore($relation['alias']) ?>_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)));
		}
<?php endforeach; ?>

<?php foreach ($this->getOneToManyRelations() as $relation): ?><?php if($relation['alias'] === 'Translation') continue;?>
		if($this->needsWidget('<?php echo $this->underscore($relation['alias']) ?>_list')){
			$this->setWidget('<?php echo $this->underscore($relation['alias']) ?>_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'expanded' => true)));
			$this->setValidator('<?php echo $this->underscore($relation['alias']) ?>_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => '<?php echo $relation['table']->getOption('name') ?>', 'required' => false)));
		}
<?php endforeach; ?>

<?php foreach ($this->getOneToOneRelations() as $relation): ?><?php if($relation['alias'] === 'Translation') continue;?>
		if($this->needsWidget('<?php echo $this->underscore($relation['alias']) ?>_list')){
			$this->setWidget('<?php echo $this->underscore($relation['alias']) ?>_list', new <?php echo $this->getWidgetClassForColumn($relation instanceof Doctrine_Relation_LocalKey ? $relation : new dmDoctrineColumn($relation['local'], $relation['table'])) ?>(array('multiple' => false, 'model' => '<?php echo $relation['table']->getOption('name')?>', 'expanded' => <?php echo $this->table->isPaginatedColumn($relation['local']) ? 'true' : 'false'?>)));
			$this->setValidator('<?php echo $this->underscore($relation['alias']) ?>_list', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => '<?php echo $relation['table']->getOption('name')?>', 'required' => true)));
		}
<?php endforeach; ?>

    
<?php if ($this->table->hasI18n()): ?>
    $this->mergeI18nForm();

<?php endif; ?>

    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->modelName) ?>_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

<?php foreach ($this->getManyToManyRelations() as $relation): ?>
  public function add<?php echo sfInflector::camelize($relation['alias']) ?>ListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.<?php echo $relation['refTable']->getOption('name') ?> <?php echo $relation['refTable']->getOption('name') ?>')
          ->andWhereIn('<?php echo $relation['refTable']->getOption('name') ?>.<?php echo $relation->getForeignFieldName() ?>', $values);
  }

<?php endforeach; ?>
  public function getModelName()
  {
    return '<?php echo $this->modelName ?>';
  }

  public function getFields()
  {
    return array(
<?php foreach ($this->getAllColumns() as $column): ?>
      '<?php echo $column->getFieldName() ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getFieldName())) ?> => '<?php echo $this->getType($column) ?>',
<?php endforeach; ?>
<?php foreach ($this->getManyToManyRelations() as $relation): ?>
      '<?php echo $this->underscore($relation['alias']) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($relation['alias']).'_list')) ?> => 'ManyKey',
<?php endforeach; ?>
    );
  }
}
