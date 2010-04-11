<?php

abstract class dmFormFilterDoctrine extends sfFormFilterDoctrine
{

  protected function mergeI18nForm($culture = null)
  {
    $this->mergeForm($this->createI18nForm());
  }

  public function isI18n()
  {
    return $this->getTable()->hasI18n();
  }

  /**
   * Create current i18n form
   */
  protected function createI18nForm($culture = null)
  {
    if (!$this->isI18n())
    {
      throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $i18nFormClass = $this->getI18nFormClass();

    $i18nForm = new $i18nFormClass();

    unset($i18nForm['id'], $i18nForm['lang']);

    return $i18nForm;
  }

  protected function getI18nFormClass()
  {
    return $this->getTable()->getI18nTable()->getComponentName().'FormFilter';
  }

  protected function getRootAlias(Doctrine_Query $query, $fieldName)
  {
    return $this->getTable()->isI18nColumn($fieldName)
    ? $query->getRootAlias().'Translation'
    : $query->getRootAlias();
  }

  protected function addForeignKeyQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($value))
    {
      $query->andWhereIn(sprintf('%s.%s', $this->getRootAlias($query, $fieldName), $fieldName), $value);
    }
    else
    {
      $query->addWhere(sprintf('%s.%s = ?', $this->getRootAlias($query, $fieldName), $fieldName), $value);
    }
  }

  protected function addEnumQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);

    $query->addWhere(sprintf('%s.%s = ?', $this->getRootAlias($query, $fieldName), $fieldName), $value);
  }

  protected function addTextQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere(sprintf('(%s.%s IS NULL OR %1$s.%2$s = ?)', $this->getRootAlias($query, $fieldName), $fieldName), array(''));
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $query->addWhere(sprintf('%s.%s LIKE ?', $this->getRootAlias($query, $fieldName), $fieldName), '%'.$values['text'].'%');
    }
  }

  protected function addNumberQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere(sprintf('(%s.%s IS NULL OR %1$s.%2$s = ?)', $this->getRootAlias($query, $fieldName), $fieldName), array(''));
    }
    else if (is_array($values) && isset($values['text']) && '' !== $values['text'])
    {
      $query->addWhere(sprintf('%s.%s = ?', $this->getRootAlias($query, $fieldName), $fieldName), $values['text']);
    }
  }

  protected function addBooleanQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);
    $query->addWhere(sprintf('%s.%s = ?', $this->getRootAlias($query, $fieldName), $fieldName), $value);
  }

  protected function addDateQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    switch($values)
    {
      case null:
      case '':
        break;
      case 'today':
        $query->andWhere(
          sprintf('%s.%s >= ?', $this->getRootAlias($query, $fieldName), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 day'))
        );
        break;
      case 'week':
        $query->andWhere(
          sprintf('%s.%s >= ?', $this->getRootAlias($query, $fieldName), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 week'))
        );
        break;
      case 'month':
        $query->andWhere(
          sprintf('%s.%s >= ?', $this->getRootAlias($query, $fieldName), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 month'))
        );
        break;
      case 'year':
        $query->andWhere(
          sprintf('%s.%s >= ?', $this->getRootAlias($query, $fieldName), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 year'))
        );
        break;
    }
  }
}