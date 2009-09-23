<?php

class dmWidgetActions extends dmFrontBaseActions
{

  public function executeEdit(sfWebRequest $request)
  {
    $this->forwardSecureUnless($this->getUser()->can('widget_edit'), 'Insufficient privileges');

    $this->forward404Unless($widget = dmDb::table('DmWidget')->find($request->getParameter('widget_id')));

    if (!$widgetType = $this->context->get('widget_type_manager')->getWidgetTypeOrNull($widget))
    {
      return $this->renderText(sprintf('<p class="s16 s16_error">%s</p><div class="clearfix mt30"><a class="dm cancel close_dialog button mr10">%s</a><a class="dm delete button red" title="%s">%s</a></div>',
        dm::getI18n()->__('The widget can not be rendered because its module does not exist anymore.'),
        dm::getI18n()->__('Cancel'),
        dm::getI18n()->__('Delete this widget'),
        dm::getI18n()->__('Delete')
      ));
    }
    
    $formClass = $widgetType->getFormClass();
    
    $form = new $formClass($widget);

    if ($request->isMethod('post'))
    {
      $form->bind();

      if ($form->isValid())
      {
        $widget->values   = $form->getWidgetValues();
        $widget->cssClass = $form->getValue('cssClass');
        
        if ($request->hasParameter('and_save'))
        {
          $widget->save();
          return $this->renderText('ok');
        }

        $helper = $this->context->get('page_helper');

        $form = new $formClass($widget);
        
        $widgetArray = $widget->toArray();

        return $this->renderText(
          $this->renderEdit($widget, $form, $widgetType).
          '__DM_SPLIT__'.
          $helper->renderWidgetInner($widgetArray).
          '__DM_SPLIT__'.
          implode('__DM_SPLIT__', $helper->getWidgetContainerClasses($widgetArray))
        );
      }
    }

    return $this->renderText($this->renderEdit($widget, $form, $widgetType));
  }

  protected function renderEdit(DmWidget $widget, dmWidgetBaseForm $form, dmWidgetType $widgetType)
  {
    return sprintf(
      '<div class="dm dm_widget_edit {form_class: \'%s\'}">%s</div>',
      $widgetType->getFullKey().'Form',
      $form->render('.dm_form.list.little')
    );
  }

  public function executeGetInner(sfWebRequest $request)
  {
    $this->forwardSecureUnless(
      $this->getUser()->can('widget_edit')
    );

    $this->forward404Unless(
      $widget = dmDb::table('DmWidget')->find($request->getParameter('widget_id'))
    );

    $helper = $this->context->get('page_helper');

    $widgetArray = $widget->toArray();
    
    return
    $this->renderText(
      $helper->renderWidgetInner($widgetArray).
      '__DM_SPLIT__'.
      implode('__DM_SPLIT__', $helper->getWidgetContainerClasses($widgetArray))
    );
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forwardSecureUnless(
      $this->getUser()->can('widget_delete')
    );

    $this->forward404Unless(
      $this->widget = dmDb::table('DmWidget')->find($request->getParameter('widget_id'))
    );

    $this->widget->delete();

    return $this->renderText('ok');
  }

  public function executeSort(sfWebRequest $request)
  {
    $this->forward404Unless(
      $zone = dmDb::table('DmZone')->find($request->getParameter('dm_zone')),
      'Can not find zone'
    );

    $this->forward404Unless(
      $widgetList = $request->getParameter('dm_widget'),
      'Missing widget list'
    );

    $this->sortWidgets($widgetList);

    return $this->renderText('ok');
  }

  public function executeAdd(sfWebRequest $request)
  {
    $this->forward404Unless(
      $toZone = dmDb::table('DmZone')->find($request->getParameter('to_dm_zone')),
      'Can not find to zone'
    );

    $this->forward404Unless(
      $widgetModule = $request->getParameter('mod'),
      'Can not find widget module'
    );

    $this->forward404Unless(
      $widgetAction = $request->getParameter('act'),
      'Can not find widget action'
    );

    $widgetType = $this->context->get('widget_type_manager')->getWidgetType($widgetModule, $widgetAction);

    $formClass = $widgetType->getFormClass();
    $form = new $formClass($widgetType->getNewWidget());

    $widget = dmDb::create('DmWidget', array(
      'module' => $widgetModule,
      'action' => $widgetAction,
      'dm_zone_id' => $toZone->id,
      'values' => $form->getDefaults()
    ))->saveGet();

    $helper = $this->context->get('page_helper');

    return $this->renderText($helper->renderWidget($widget->toArray(), true));
  }

  public function executeMove(sfWebRequest $request)
  {
    $this->forward404Unless(
      $widget = dmDb::table('DmWidget')->find($request->getParameter('moved_dm_widget')),
      'Can not find widget'
    );

    $this->forward404Unless(
      $toZone = dmDb::table('DmZone')->find($request->getParameter('to_dm_zone')),
      'Can not find to zone'
    );

    $this->forward404Unless(
      $widgetList = $request->getParameter('dm_widget'),
      'Missing widget list'
    );

    $widget->set('dm_zone_id', $toZone->id)->save();

    $this->sortWidgets($widgetList);

    return $this->renderText('ok');
  }

  protected function sortWidgets(array $widgetList)
  {
    $widgets = array();

    foreach($widgetList as $position => $widgetId)
    {
      $widgets[$widgetId] = $position+1;
    }

    try
    {
      dmDb::table('DmWidget')->doSort($widgets);
    }
    catch(Exception $e)
    {
      if ($this->getUser()->can('system'))
      {
        throw $e;
      }

      $this->getUser()->logError(dm::getI18n()->__('A problem occured when sorting the items'));
    }
  }
}