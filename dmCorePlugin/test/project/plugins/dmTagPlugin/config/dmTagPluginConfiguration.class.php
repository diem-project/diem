<?php

class dmTagPluginConfiguration extends sfPluginConfiguration
{
  public function configure()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this,'listenToDmContextLoaded'));
  }

  public function listenToDmContextLoaded(sfEvent $e)
  {
    if($this->configuration instanceof dmAdminApplicationConfiguration)
    {
      $this->dispatcher->connect('form.post_configure', array($this, 'listenToFormPostConfigureEvent'));
    }

    $this->dispatcher->connect('dm.admin_generator_builder.config', array($this, 'listenToAdminGeneratorBuilderConfig'));

    $this->dispatcher->connect('dm.table.filter_seo_columns', array($this, 'listenToTableFilterSeoColumns'));
  
    $this->dispatcher->connect('dm.admin_generator.post_configure', array($this, 'listenToAdminGeneratorPostConfigureEvent'));
  }

  public function listenToAdminGeneratorPostConfigureEvent(sfEvent $event)
  {
    if($event['table'] instanceof DmTagTable)
    {
      $event['table']->loadTaggableModels();
    }
  }

  public function listenToAdminGeneratorBuilderConfig(sfEvent $event, array $config)
  {
    if($event['module']->getTable()->hasTemplate('DmTaggable'))
    {
      foreach($config['form']['display'] as $fieldset => $fields)
      {
        if(false !== ($tagsListPosition = array_search('tags_list', $fields)))
        {
          $config['form']['display'][$fieldset][$tagsListPosition] = 'tags';
        }
      }
    }

    return $config;
  }

  public function listenToTableFilterSeoColumns(sfEvent $event, array $seoColumns)
  {
    if($event->getSubject()->hasTemplate('DmTaggable'))
    {
      $seoColumns[] = 'tags_string';
    }

    return $seoColumns;
  }

  public function listenToFormPostConfigureEvent(sfEvent $event)
  {
    $form = $event->getSubject();

    if($form instanceof dmFormDoctrine && $form->getObject()->getTable()->hasTemplate('DmTaggable'))
    {
      $form->setWidget('tags', new sfWidgetFormDmTagsAutocomplete(
        array('choices' => $form->getObject()->getTagNames())
      ));

      $form->setValidator('tags', new sfValidatorDmTagsAutocomplete(array(
        'required' => false
      )));
    }
  }
}