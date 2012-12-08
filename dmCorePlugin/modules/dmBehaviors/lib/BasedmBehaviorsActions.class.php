<?php

/**
 * Actions for managing the behaviors
 * It is used for administration of behaviors purposes
 * Except logBehaviorException action, which reports the problem with execution of the behavior JS (init() or start() method)
 *
 * @author TheCelavi
 */
class BasedmBehaviorsActions extends dmBaseActions {
    
    /**
     * Since behaviors are client side it is hard to find out is there are some problem
     * with executed behavior javascript. This method is convinient wat to save error
     * output from the client raised in production in order to debug later on.
     */
    public function executeLogBehaviorException(dmWebRequest $request) {
        if ($request->hasParameter('dm_behavior_error')) {
            $error = new DmError();
            $error->setName(sprintf('Behavior: Method %s in view caused a problem', $request->getParameter('method')));
            $description = 
            $error->setDescription(sprintf('Method: %s; JavaScript exception: %s; Behavior settings: %s',
                    $request->getParameter('method'),
                    $request->getParameter('javascript_exception'),
                    var_export($request->getParameter('behavior_settings'), true)
            ));
            $error->setPhpClass('N/A');
            $error->setModule($request->getParameter('page_module'));
            $error->setAction($request->getParameter('page_action'));
            $error->setUri(sprintf('Module "%s" @ action "%s" with page id "%s" and culture "%s"', 
                    $request->getParameter('page_module'), 
                    $request->getParameter('page_action'), 
                    $request->getParameter('page_id'),
                    $request->getParameter('page_culture')));
            $error->setEnv(sprintf('Unknown, check file: %s', str_replace('/', '', $request->getParameter('script_name'))));
            $error->save();
        }
        return sfView::NONE;
    }
    
    /**
     * Utility function
     * Gets the privileges, translations, etc...
     * WARNING: This is only to speed up the javascript regarding the privileges, 
     * methods should check for the privileges before any modification of the database
     */
    public function executeGetBehaviorsManagerSettings(dmWebRequest $request) {
        if ($this->getUser()->can('behavior_add') || $this->getUser()->can('behavior_edit') || $this->getUser()->can('behavior_delete') || $this->getUser()->can('behavior_sort')) {
            $settings = array(
                'privileges' => array(
                    'add' => (bool)$this->getUser()->can('behavior_add'),
                    'edit' => (bool)$this->getUser()->can('behavior_edit'),
                    'del' => (bool)$this->getUser()->can('behavior_delete'),
                    'sort' => (bool)$this->getUser()->can('behavior_sort')
                ),
                'page_attachable' => $this->getService('behaviors_manager')->isPageAttachable()
            );
            return $this->renderJson($settings);
        } else throw new dmException ('Possible hack - someone tryied to get behaviorManager settings without sufficient privileges');
    }
    
    /**
     * Builds a template for context menu that will be used for the administration of the behaviors
     * Purpose is to speed up the administration by decreasing the HTTP requests
     * Template is executed once, with all behaviors that can be edited, deleted or sorted
     */
    public function executeBuildContextMenuItems(dmWebRequest $request) {
        $behaviorsManager = $this->getService('behaviors_manager');
        $behaviors = $behaviorsManager->getListOfRegisteredBehaviors();
        $html = '';
        if ($this->getUser()->can('behavior_delete') || $this->getUser()->can('behavior_edit')) {
            foreach ($behaviors as $key => $behavior) {
                $html .= sprintf(
                        '<li id="dm_behavior_cm_item_%s" class="dm_behavior_cm_item">
                            <span>
                                <img width="16" height="16" src="%s"/>%s
                             </span>
                         </li>'
                         , $key, $behavior['icon'], $this->getI18n()->__($behavior['name']));
            }
            if ($this->getUser()->can('behavior_sort')) 
                $html .= sprintf(
                        '<li id="dm_behavior_cm_sort">
                            <span class="s16 s16_sort"></span>%s
                         </li>', 
                        $this->getI18n()->__('Sort behaviors'));
            $html = sprintf('<ul>%s</ul>', $html);
        }
        return $this->renderAsync(array('html'=>$html));
    }
    
    /**
     * Creates an empty instance of the behavior based on its key
     * Returns JSON with default behavior data or error
     */    
    public function executeAdd(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_add')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to add behavior'));
        
        if ($request->hasParameter('dm_behavior_key') && $request->hasParameter('dm_action_add') && $request->getParameter('dm_action_add')) {
            $behaviorsManager = $this->getService('behaviors_manager');
            if (!$behaviorsManager->isExists($request->getParameter('dm_behavior_key'))) 
                    return $this->renderError (
                            $this->getI18n ()->__('Error'), 
                            $this->getI18n()->__('The behavior with key "%key%" does not exists aymore', array('%key%'=>$request->getParameter('dm_behavior_key')))
                            );
            
            try {
                $behavior = $behaviorsManager->createEmptyInstance(
                        $request->getParameter('dm_behavior_key'),
                        $request->getParameter('dm_behavior_attached_to'),
                        $request->getParameter('dm_behavior_attached_to_id'),
                        ($request->getParameter('dm_behavior_attached_to_content') == 'true') ? $request->getParameter('dm_behavior_attached_to_selector') : null
                        );
            } catch (Exception $e) {
                return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n ()->__('The behavior could not be created'));
            }
            try {
                $viewClass = $behaviorsManager->getBehaviorViewClass($request->getParameter('dm_behavior_key'));
                $behaviorView = new $viewClass($this->context, $behavior);
            } catch (Exception $e) {
                return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n()->__('Behavior created, but could not initialize behavior view class'));
            }
            
            return $this->renderJson(array(
                'error' => false,              
                'dm_behavior_data' => $behaviorView->renderArray(),
                'js' => $this->parseJavascripts($behaviorView->getJavascripts()),
                'css' => $this->parseStylesheets($behaviorView->getStylesheets())
            ));
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('Behavior is not created'));
    }
    
    /**
     * Deletes behavior based on behavior key
     * Returns JSON conformation
     */
    public function executeDelete(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_delete')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to delete behavior'));
        
        if ($request->hasParameter('dm_behavior_id') && $request->hasParameter('dm_action_delete') && $request->getParameter('dm_action_delete')) {
            $behaviorsManager = $this->getService('behaviors_manager');
            try {
                $behavior = $behaviorsManager->getDmBehavior($request->getParameter('dm_behavior_id'));
                $behavior->delete();
            } catch (Exception $e) {
                return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n ()->__('The behavior does not exists aymore'));
            }
            return $this->renderJson(array(
                'error'=>false
            ));
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('Behavior is not deleted'));
    }
    
    /**
     * Edit the behavior
     * Return HTML for form that ought to be rendered
     * Or JSON if it has error, or changes are saved
     */
    public function executeEdit(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_edit')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to delete behavior'));
        
        if ($request->hasParameter('dm_behavior_id') && $request->hasParameter('dm_action_edit') && $request->getParameter('dm_action_edit')) {
            $behaviorsManager = $this->getService('behaviors_manager');
            try {
                $behavior = $behaviorsManager->getDmBehavior($request->getParameter('dm_behavior_id'));
            } catch (Exception $e) {
                return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n ()->__('The behavior does not exists aymore'));
            }
            try {
                $formClass = $behaviorsManager->getBehaviorFormClass($behavior->getDmBehaviorKey());
                $viewClass = $behaviorsManager->getBehaviorViewClass($behavior->getDmBehaviorKey());
                $form = new $formClass($behavior);
                $view = new $viewClass($this->context, $behavior);
            } catch (Exception $e) {
                return $this->renderAsync(array(
                    'html'  => $this->renderFormNotExist()
                ));
            }
            if ($request->isMethod('post') && $form->bindAndValid($request)){
                $form->updateBehavior();
                if ($request->hasParameter('and_save')) {
                    $behavior->save();
                    return $this->renderJson(array(
                        'error' => false,              
                        'dm_behavior_data' => $view->renderArray(),
                        'js' => $this->parseJavascripts($view->getJavascripts()),
                        'css' => $this->parseStylesheets($view->getStylesheets())
                    ));
                }
            }            
            return $this->renderAsync(array(
              'html'  => $this->renderEdit($form, $behaviorsManager, $request->isMethod('get')),
              'js'    => array_merge(array('lib.hotkeys'), $form->getJavascripts()),
              'css'   => $form->getStylesheets()
            ), true);
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You must supply behavior for edit'));
    }
    
    /**
     * Utility function
     * Renders the form for the behavior edit
     * @param dmBehaviorBaseForm $form
     * @param dmBehaviorsManager $behaviorsManager
     * @param boolean $withCopyActions
     * @return rendered form
     */
    protected function renderEdit(dmBehaviorBaseForm $form, $behaviorsManager, $withCopyActions = true) {
        $helper = $this->getHelper();
        $copyActions = '';
        if ($withCopyActions && $this->getUser()->can('behavior_add')) {
            $copyActions .= $helper->tag('div.dm_cut_copy_actions.none',
            
            (($this->getUser()->can('behavior_delete')) ? $helper->link('+/dmBehaviors/cut') // User can not cut if he can not delete!
            ->param('dm_behavior_id', $form->getDmBehavior()->get('id'))
            ->text('')
            ->title($this->getI18n()->__('Cut'))
            ->set('.s16block.s16_cut.dm_behavior_cut') : '').
            
            $helper->link('+/dmBehaviors/copy')
            ->param('dm_behavior_id', $form->getDmBehavior()->get('id'))
            ->text('')
            ->title($this->getI18n()->__('Copy'))
            ->set('.s16block.s16_copy.dm_behavior_copy')
          );
        }
        return $helper->tag('div.dm.dm_behavior_edit.dm_behavior_edit_form.'.dmString::underscore($form->getDmBehavior()->get('dm_behavior_key')).'_form',
            array('json' => array('form_class' => $behaviorsManager->getBehaviorFormClass($form->getDmBehavior()->get('dm_behavior_key')), 'form_name' => $form->getName())),
            $form->render('.dm_form.list.little').$copyActions
        );
        
    }

    /**
     * Sort sequence of execution of the behaviors for the page content of part of layout area     
     */
    public function executeSortBehaviors(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_sort')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to sort behaviors'));
        
        
        if ($request->hasParameter('dm_behavior_attached_to') && $request->hasParameter('dm_behavior_attached_to_id') && $request->hasParameter('dm_action_sort') && $request->getParameter('dm_action_sort')) {
            $behaviorsManager = $this->getService('behaviors_manager');
            try {
                $behaviors = $behaviorsManager->getBehaviorsForSort($request->getParameter('dm_behavior_attached_to'), $request->getParameter('dm_behavior_attached_to_id'));
            } catch (Exception $e) {
                return $this->renderText(
                        sprintf('<p class="s16 s16_error">%s</p>
                            <div class="clearfix mt30"><a class="dm cancel close_dialog button mr10">%s</a></div>',
                  $this->getI18n()->__('There are no behaviors for this content to be sorted.'),
                  $this->getI18n()->__('Cancel')
                ));
            }
            try {
                $form = new dmBehaviorsSortForm($behaviors);
            } catch (Exception $e) {
                return $this->renderError (
                    $this->getI18n ()->__('Error'),
                    $e->getMessage());
            }
            if ($request->isMethod('post') && $form->bindAndValid($request) && $request->hasParameter('and_save')){
                if ($form->saveSortOrder()) return $this->renderJson(array(
                    'error' => false,              
                    'dm_behavior_data' => json_decode ($form->getValue('behaviors'), true)
                ));
                else return $this->renderError (
                    $this->getI18n ()->__('Error'),
                    $this->getI18n ()->__('New behaviors sequence could not be saved.'));
            }
            return $this->renderAsync(array(
              'html'  => $this->getHelper()->tag('div.dm.dm_behavior_sort.dm_behavior_sort_form' , $form->render('.dm_form.list.little'))
            ), true);
            
            
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You must supply container for sort'));
    }
    
    /**
     * Paste behavior from clipboard
     * returns behavior settings, css, js and action which caused a paste
     */
    public function executePaste(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_add')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to paste behaviors'));
        if (!$this->getUser()->can('behavior_delete') && $request->getParameter('clipboard_action') == 'cut') return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to cut behaviors'));
        
        $clipboard = $this->getUser()->getAttribute('dm_behavior_clipboard', null, 'dm.front_user_behavior_clipboard');
        if (is_null($clipboard)) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('The clipboard is empty'));
        if ($clipboard['dm_behavior_clipboard_action'] != $request->getParameter('dm_behavior_clipboard_action')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('Unknown error occured - cliboard action missmatch'));
        $behaviorsManager = $this->getService('behaviors_manager');
        
        $behavior = DmBehaviorTable::getInstance()->findOneById($clipboard['dm_behavior_id']);
        if (!$behavior) {
            $this->getUser()->setAttribute('dm_behavior_clipboard', null, 'dm.front_user_behavior_clipboard');
            return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('The behavior from the clipboard does not exist anymore'));
        }
        $clipboardBehavior = $behavior;
        if ($clipboard['dm_behavior_clipboard_action'] == 'copy') {
            $clipboardBehavior = new DmBehavior();
            try {
                $position = dmDb::query('DmBehavior b')                
                    ->orderBy('b.position desc')
                    ->limit(1)
                    ->select('MAX(b.position) as position')
                    ->fetchOneArray();
                $clipboardBehavior->setPosition($position['position']+1);
            } catch (Exception $e) {
                return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n ()->__('The behavior from clipboard could not be created'));
            }
        }
        $clipboardBehavior->setDmBehaviorKey($behavior->getDmBehaviorKey());
        $clipboardBehavior->setDmBehaviorAttachedTo($request->getParameter('dm_behavior_attached_to'));
        if ($request->getParameter('dm_behavior_attached_to_content') == 'true') $clipboardBehavior->setDmBehaviorAttachedToSelector($request->getParameter('dm_behavior_attached_to_selector'));
        else $clipboardBehavior->setDmBehaviorAttachedToSelector(null);
        $clipboardBehavior->setDmPageId(null);
        $clipboardBehavior->setDmAreaId(null);
        $clipboardBehavior->setDmZoneId(null);
        $clipboardBehavior->setDmWidgetId(null);
        switch ($request->getParameter('dm_behavior_attached_to')) {
            case 'page': $clipboardBehavior->setDmPageId($request->getParameter('dm_behavior_attached_to_id')); break;
            case 'area': $clipboardBehavior->setDmAreaId($request->getParameter('dm_behavior_attached_to_id')); break;
            case 'zone': $clipboardBehavior->setDmZoneId($request->getParameter('dm_behavior_attached_to_id')); break;
            case 'widget': $clipboardBehavior->setDmWidgetId($request->getParameter('dm_behavior_attached_to_id')); break;
            default : return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('The behavior can not be attached to unknown container')); break;
        }
        $clipboardBehavior->setDmBehaviorValue($behavior->getDmBehaviorValue());
        $clipboardBehavior->setDmBehaviorEnabled($behavior->getDmBehaviorEnabled());
        try {
            $clipboardBehavior = $clipboardBehavior->saveGet();
        } catch(Exception $e) {
            return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n ()->__('The behavior from clipboard could not be created'));
        }
        try {
            $viewClass = $behaviorsManager->getBehaviorViewClass($clipboardBehavior->getDmBehaviorKey());
            $behaviorView = new $viewClass($this->context, $clipboardBehavior);
        } catch (Exception $e) {
            return $this->renderError($this->getI18n ()->__('Error'), $this->getI18n()->__('Behavior created, but could not initialize behavior view class'));
        }        
        return $this->renderJson(array(
            'error' => false,  
            'dm_behavior_clipboard_action' => $clipboard['dm_behavior_clipboard_action'],
            'dm_behavior_data' => $behaviorView->renderArray(),
            'js' => $this->parseJavascripts($behaviorView->getJavascripts()),
            'css' => $this->parseStylesheets($behaviorView->getStylesheets())
        ));
        
    }
    
    /**
     * Stores behavior id and cut action in clipboard     
     */
    public function executeCut(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_add') && !$this->getUser()->can('behavior_delete')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to cut behavior'));
        
        if ($request->hasParameter('dm_behavior_id')) {   
            $behavior = DmBehaviorTable::getInstance()->findOneById($request->getParameter('dm_behavior_id'));
            if (!$behavior) return $this->renderError (
                    $this->getI18n ()->__('Error'),
                    $this->getI18n ()->__('The behavior that you are cutting does not exist anymore'));
            $this->getUser()->setAttribute('dm_behavior_clipboard', array(
                'dm_behavior_clipboard_action'            =>      'cut',
                'dm_behavior_id'                          =>     $behavior->getId()
            ), 'dm.front_user_behavior_clipboard');
            return $this->renderJson(array(
                    'error' => false
                ));
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You must supply behavior for cuting'));
    }
    
    /**
     * Stores behavior id and copy action in clipboard     
     */
    public function executeCopy(dmWebRequest $request) {
        if (!$this->getUser()->can('behavior_add')) return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You do not have privileges to copy behavior'));        
        
        if ($request->hasParameter('dm_behavior_id')) {   
            $behavior = DmBehaviorTable::getInstance()->findOneById($request->getParameter('dm_behavior_id'));
            if (!$behavior) return $this->renderError (
                    $this->getI18n ()->__('Error'),
                    $this->getI18n ()->__('The behavior that you are copying does not exist anymore'));
            $this->getUser()->setAttribute('dm_behavior_clipboard', array(
                'dm_behavior_clipboard_action'            =>      'copy',
                'dm_behavior_id'                          =>     $behavior->getId()
            ), 'dm.front_user_behavior_clipboard');
            return $this->renderJson(array(
                    'error' => false
                ));
        } else return $this->renderError (
                $this->getI18n ()->__('Error'),
                $this->getI18n ()->__('You must supply behavior for copying'));
    }    

    /**
     * Utility function
     * Reloads add behaviors menu
     */
    public function executeReloadAddMenu(dmWebRequest $request) {
        $menu = $this->getService('behaviors_add_menu');
        $menu->build()->render();
        $menu .= '<li class="dm_behaviors_menu_actions clearfix">' .
                '<input class="dm_add_behaviors_search" title="' . $this->getI18n()->__('Search for a behavior') . '" />' .
                (($this->getUser()->can('behavior_sort')) ? '<a class="dm_sort_all_behaviors"><span class="s16 s16_sort"></span>' . $this->getI18n()->__('Sort content behaviors') . '</a>' : '') .
                '</li>';
        return $this->renderText($menu);
    }

    /**
     * Utility function
     * Renders a JSON error for behavior manager
     * @param type string
     * @param type string
     * @return JSON
     */
    protected function renderError($title, $message) {
        return $this->renderJson(array(
            'error' => array(
                'title' => $title,
                'message' => $message
            )
        ));
    }

    /**
     * Utility function
     * Renders form for the behavior that is deinstalled in the meantime
     * @return HTML code
     */
    protected function renderFormNotExist() {
        return sprintf(
                '<p class="s16 s16_error">%s</p>
                    <div class="clearfix mt30">
                    <a class="dm cancel close_dialog button mr10">%s</a>
                    %s
                 </div>',
            $this->getI18n()->__('The behavior can not be rendered because its type does not exist anymore.'),
            $this->getI18n()->__('Cancel'),
            ($this->getUser()->can('behavior_delete')) ? sprintf('<a class="dm delete button red" title="%s">%s</a>', $this->getI18n()->__('Delete this behavior'), $this->getI18n()->__('Delete')) : ''
        );
    }
    /**
     * Utility function
     * Parse javascript file paths required for behavior to be executed/rendered
     * @param array $javascripts
     * @return array of javascripts
     */
    protected function parseJavascripts($javascripts) {
        if (!is_array($javascripts)) $javascripts = array($javascripts);
        foreach ($javascripts as &$js) {
            $js = $this->getHelper()->getJavascriptWebPath($js);
        }
        return $javascripts;
    }
    
    /**
     * Utility function
     * Parse stylesheet file paths required for behavior to be rendered
     * @param array $stylesheets
     * @return array of stylesheets 
     */
    protected function parseStylesheets($stylesheets) {
        if (!is_array($stylesheets)) $stylesheets = array($stylesheets);
        foreach ($stylesheets as &$css) {
            $css = $this->getHelper()->getStylesheetWebPath($css);
        }
        return $stylesheets;
    }


}

