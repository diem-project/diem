<?php

abstract class dmFormFilterDoctrine extends sfFormFilterDoctrine
{

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
          sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 day'))
        );
        break;
      case 'week':
        $query->andWhere(
          sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 week'))
        );
        break;
      case 'month':
        $query->andWhere(
          sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 month'))
        );
        break;
      case 'year':
        $query->andWhere(
          sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName),
          date('Y-m-d H:i:s', strtotime('-1 year'))
        );
        break;
    }
  }
}