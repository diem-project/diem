<?php
/**
 */
class PluginDmMailTemplateTable extends myDoctrineTable
{

  public function getAdminListQuery(dmDoctrineQuery $query)
  {
    return $query
    ->withI18n();
  }
}