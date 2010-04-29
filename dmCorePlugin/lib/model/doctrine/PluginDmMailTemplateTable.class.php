<?php
/**
 */
class PluginDmMailTemplateTable extends myDoctrineTable
{

  public function createDefault($name)
  {
    return $this->create(array(
      'name' => $name,
      'subject' => 'Default subject',
      'from_email' => 'noreply@nomail.com'
    ));
  }

  public function findOneByNameWithI18n($name)
  {
    return $this->createQuery('mt')
    ->withI18n()
    ->where('mt.name = ?', $name)
    ->fetchOne();
  }

  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    return $query
    ->withI18n();
  }
}