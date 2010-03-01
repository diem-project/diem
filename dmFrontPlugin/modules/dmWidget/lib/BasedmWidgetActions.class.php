<?php

class BasedmWidgetActions extends dmFrontBaseActions
{

  /*
   * Quickly render a widget from Ajax
   * _link('+/dmWidget/render')->param('widget_id', $widgetId)->param('page_id', $pageId)
   */
  public function executeRender(dmWebRequest $request)
  {
    return $this->renderText(
      $this->getService('page_helper')->renderWidgetInner($this->requireWidget()->toArrayWithMappedValue())
    );
  }

  public function executeEditRecord(dmWebRequest $request)
  {
    $widget = $this->requireWidget();

    $this->forward404Unless(
        $widget->action === 'show'
    &&  ($module = $this->getService('module_manager')->getModuleOrNull($widget->module))
    &&  $module->hasModel()
    &&  $module->hasAdmin()
    );

    $query = $module->getTable()->createQuery('r');

    if ($widget->getValueByKey('recordId'))
    {
      $query->addWhere('r.id = ?', $widget->getValueByKey('recordId'))->fetchRecord();
    }
    elseif ($this->getPage()->getDmModule()->hasModel())
    {
      $query->whereDescendantId($this->getPage()->getDmModule()->getModel(), $this->getPage()->get('record_id'), $module->getModel());
    }

    $this->forward404Unless($record = $query->fetchRecord());

    return $this->renderAsync(array(
      'html'  => $this->getHelper()->link('app:admin/+/'.$module->getKey().'/edit')->params(array(
        'pk'        => $record->id,
        'dm_embed'  => 1
      )),
      'js'    => array('lib.colorbox'),
      'css'   => array('lib.colorbox')
    ), true);
  }

  public function executeCopy(dmWebRequest $request)
  {
    return $this->clipboard($request, 'copy');
  }

  public function executeCut(dmWebRequest $request)
  {
    return $this->clipboard($request, 'cut');
  }

  protected function clipboard(dmWebRequest $request, $method)
  {
    $this->getService('front_clipboard')->$method($this->requireWidget());

    return $this->renderText('ok');
  }

  public function executePaste(dmWebRequest $request)
  {
    $this->forward404Unless(
      $toZone = dmDb::table('DmZone')->find($request->getParameter('to_dm_zone')),
      'Can not find to zone'
    );

    $widget = $this->getService('front_clipboard')->paste($toZone);

    return $this->renderText($this->getService('page_helper')->renderWidget($widget->toArrayWithMappedValue(), true));
  }

  public function executeEdit(dmWebRequest $request)
  {
    $widget = $this->requireWidget();
  
    try
    {
      $widgetType = $this->getService('widget_type_manager')->getWidgetType($widget);
      $formClass = $widgetType->getFormClass();
      $form = new $formClass($widget);
    }
    catch(Exception $e)
    {
      if(sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      
      return $this->renderError();
    }
    
    if ($request->isMethod('post') && $form->bindAndValid($request))
    {
      $form->updateWidget();

      if ($request->hasParameter('and_save'))
      {
        $widget->save();
        return $this->renderText('saved');
      }

      $widgetArray = $widget->toArrayWithMappedValue();

      try
      {
        $widgetRenderer = $this->getServiceContainer()
        ->setParameter('widget_renderer.widget', $widgetArray)
        ->getService('widget_renderer');
        $js  = $widgetRenderer->getJavascripts();
        $css = $widgetRenderer->getStylesheets();
      }
      catch(Exception $e)
      {
        $js = $css = array();
      }

      return $this->renderAsync(array(
        'html'  => $this->renderEdit(new $formClass($widget), $widgetType, false).dmString::ENCODING_SEPARATOR.$this->getService('page_helper')->renderWidget($widgetArray),
        'js'    => $js,
        'css'   => $css
      ), true);
    }
    
    return $this->renderAsync(array(
      'html'  => $this->renderEdit($form, $widgetType, $request->isMethod('get')),
      'js'    => array_merge(array('lib.hotkeys'), $form->getJavascripts()),
      'css'   => $form->getStylesheets()
    ), true);
  }
  
  protected function renderError()
  {
    return $this->renderText(sprintf('<p class="s16 s16_error">%s</p><div class="clearfix mt30"><a class="dm cancel close_dialog button mr10">%s</a><a class="dm delete button red" title="%s">%s</a></div>',
      $this->getI18n()->__('The widget can not be rendered because its type does not exist anymore.'),
      $this->getI18n()->__('Cancel'),
      $this->getI18n()->__('Delete this widget'),
      $this->getI18n()->__('Delete')
    ));
  }

  protected function renderEdit(dmWidgetBaseForm $form, dmWidgetType $widgetType, $withCopyActions = true)
  {
    $helper = $this->getHelper();

    $devActions= '';
    if ($this->getUser()->can('code_editor') && $form instanceof dmWidgetProjectForm)
    {
      $templateDir = dmOs::join(sfConfig::get('sf_app_module_dir'), $form->getDmModule()->getKey(), 'templates', '_'.$form->getDmComponent()->getKey().'.php');
      if (file_exists($templateDir))
      {
        $devActions .= '<a href="#'.dmProject::unRootify($templateDir).'" class="code_editor s16 s16_code_editor block">'.$this->getI18n()->__('Edit template code').'</a>';
      }
      
      $componentDir = dmOs::join(sfConfig::get('sf_app_module_dir'), $form->getDmModule()->getKey(), 'actions/components.class.php');
      if (file_exists($componentDir))
      {
        $devActions .= '<a href="#'.dmProject::unRootify($componentDir).'" class="code_editor s16 s16_code_editor block">'.$this->getI18n()->__('Edit component code').'</a>';
      }
    }

    if ($devActions)
    {
      $devActions = '<div class="code_editor_links">'.$devActions.'</div>';
    }

    $copyActions = '';
    if ($withCopyActions && $this->getUser()->can('widget_add'))
    {
      $copyActions = $helper->tag('div.dm_cut_copy_actions.none',
        $helper->link('+/dmWidget/cut')
        ->param('widget_id', $form->getDmWidget()->get('id'))
        ->text('')
        ->title($this->getI18n()->__('Cut'))
        ->set('.s16block.s16_cut.dm_widget_cut').
        $helper->link('+/dmWidget/copy')
        ->param('widget_id', $form->getDmWidget()->get('id'))
        ->text('')
        ->title($this->getI18n()->__('Copy'))
        ->set('.s16block.s16_copy.dm_widget_copy')
      );
    }
    
    return $helper->tag('div.dm.dm_widget_edit.'.dmString::underscore($widgetType->getFullKey()).'_form',
    array('json' => array('form_class' => $widgetType->getFullKey().'Form', 'form_name' => $form->getName())),
    $form->render('.dm_form.list.little').$devActions.$copyActions
    );
  }

  public function executeGetFull(sfWebRequest $request)
  {
    $widgetArray = $this->requireWidget()->toArrayWithMappedValue();

    try
    {
      $widgetRenderer = $this->getServiceContainer()
      ->setParameter('widget_renderer.widget', $widgetArray)
      ->getService('widget_renderer');
      $js  = $widgetRenderer->getJavascripts();
      $css = $widgetRenderer->getStylesheets();
    }
    catch(Exception $e)
    {
      $js = $css = array();
    }

    return $this->renderAsync(array(
      'html'  => $this->getService('page_helper')->renderWidget($widgetArray),
      'css'   => $js,
      'js'    => $css
    ), true);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->requireWidget()->delete();

    return $this->renderText('ok');
  }

  public function executeSort(sfWebRequest $request)
  {
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

    $widgetType = $this->getService('widget_type_manager')->getWidgetType($widgetModule, $widgetAction);

    $formClass = $widgetType->getFormClass();
    $form = new $formClass($widgetType->getNewWidget());
    
    $form->removeCsrfProtection();

    $widget = dmDb::create('DmWidget', array(
      'module' => $widgetModule,
      'action' => $widgetAction,
      'dm_zone_id' => $toZone->id,
      'values' => $form->getDefaults()
    ))->saveGet();

    return $this->renderText($this->getService('page_helper')->renderWidget($widget->toArrayWithMappedValue(), true));
  }

  public function executeMove(sfWebRequest $request)
  {
    $widget = $this->requireWidget('moved_dm_widget');

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

      $this->getUser()->logError($this->getI18n()->__('A problem occured when sorting the items'));
    }
  }

  protected function requireWidget($requestParameterName = 'widget_id')
  {
    $this->forward404Unless(
      $widgetId = $this->getRequest()->getParameter($requestParameterName),
      'No '.$requestParameterName.' parameter'
    );
    
    $this->forward404Unless(
      $widget = dmDb::table('DmWidget')->findOneByIdWithI18n($widgetId),
      'No widget found'
    );

    return $widget;
  }
}