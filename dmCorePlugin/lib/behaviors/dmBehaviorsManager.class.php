<?php

/**
 * Service for managing behaviors
 *
 * @author TheCelavi
 */
class dmBehaviorsManager extends dmConfigurable {
    
    protected 
    $adminMode = false,
    $context,
    $page,
    $behaviors,
    $helper,
    $i18n,
    $loadedBehaviors,
    $javascripts,
    $stylesheets;
    
    public function __construct(dmContext $context, array $options = array()) {    
        sfContext::getInstance()->getConfigCache()->registerConfigHandler('config/dm/behaviors.yml', 'dmBehaviorsConfigHandler', array());
        include sfContext::getInstance()->getConfigCache()->checkConfig('config/dm/behaviors.yml');    
        $this->behaviors = sfConfig::get('dm_behaviors');
        $this->helper = dmContext::getInstance()->getServiceContainer()->getService('helper');
        $this->i18n = dmContext::getInstance()->getServiceContainer()->getService('i18n');
        $this->loadedBehaviors = array();
        $this->javascripts = array();
        $this->stylesheets = array();
        $this->context = $context;
        // The mode of instance
        $user = $this->context->getServiceContainer()->getService('user');
        
        
        
        // HELP HERE PLEASE !!!
        // MAYBE USER CAN NOT ADMINISTRATE BEHAVIORS
        // BUT IF HE CAN CHANGE CONTENT
        // THIS SHOULD BE REGISTERED AS ADMIN MODE!!!
        // WHAT MORE PRIVILEGES TO CHECK ????
        
        
        $this->adminMode = $user->can('behavior_add') || $user->can('behavior_edit') || $user->can('behavior_delete');
        
        $this->configure($options);
    }

    public function isPageAttachable() {
        return (bool) $this->getOption('page_attachable', false);
    }

    public function isAreaAttachable() {
        return (bool) $this->getOption('area_attachable', false);
    }
    
    public function isZoneAttachable() {
        return (bool) $this->getOption('zone_attachable', true);
    }
    
    public function isWidgetAttachable() {
        return (bool) $this->getOption('widget_attachable', true);
    }

    /**
     * Checks if behavior exists
     * @param string $key
     * @return boolean
     */
    public function isExists($key) {
        return (bool) isset($this->behaviors[$key]);
    }

    /**
     * Creates an empty instance of the DmBehavior
     * 
     * @param string $key The behavior key
     * @param string $attachedTo On which container is attached, page, area, zone or widget?
     * @param int $attachedToId The id of the container
     * @param string $attachedToSelector If it is attached to the content
     * @return type 
     */   
    public function createEmptyInstance($key, $attachedTo, $attachedToId, $attachedToSelector = null) {        
        $formClass = $this->getBehaviorFormClass($key);
        $behavior = new DmBehavior();
        $behavior->setDmBehaviorKey($key);
        $form = new $formClass($behavior);
        $form->removeCsrfProtection(); 
        $position = dmDb::query('DmBehavior b')                
                ->orderBy('b.position desc')
                ->limit(1)
                ->select('MAX(b.position) as position')
                ->fetchOneArray();
        $saveData = array(
            'position' => $position['position'] + 1, // Last in, first executes... :)
            'dm_behavior_key'=>$key,
            'dm_behavior_attached_to'=> $attachedTo,            
            'dm_'.$attachedTo.'_id'=> (int) $attachedToId,
            'dm_behavior_value' => json_encode($form->getDefaults()));
        if (!is_null($attachedToSelector)) $saveData['dm_behavior_attached_to_selector'] = $attachedToSelector;
        
        return dmDb::create('DmBehavior', $saveData)->saveGet();
    }

    /**
     * Renders DIV tag with loaded Behaviors metadata
     * This is slow script, but it is speeded up with cache
     * 
     * @param array $page The Page structure from Page helper
     * @return string
     */
    public function renderBehaviors($page, $areas) {
        $canCache = !$this->adminMode; // If it is in admin mode, do not use cached output
        $this->page = $page;
        if ($canCache && $cache = $this->getCache()) return $cache;
        $behaviors = $this->getBehaviorsForPage($page, $areas);
        $behaviorsSettings = array();
        
        foreach ($behaviors as $behavior) {

            if ($this->adminMode) { // We will throw errors here
                $viewClass = $this->getBehaviorViewClass($behavior->getDmBehaviorKey());
                $view = new $viewClass($this->context, $behavior);
                $behaviorsSettings[] = $view->render();
                $this->addJavascript($view->getJavascripts());
                $this->addStylesheet($view->getStylesheets());
            } else {
                try { // It is not admin mode, so we will not crash the presentation if behavior can not be rendered
                    $viewClass = $this->getBehaviorViewClass($behavior->getDmBehaviorKey());
                    $view = new $viewClass($this->context, $behavior);
                    $canCache = $canCache && $view->isCachable();
                    if (!is_null($output = $view->render())){
                        $behaviorsSettings[] = $output;
                        $this->addJavascript($view->getJavascripts());
                        $this->addStylesheet($view->getStylesheets());
                    }
                } catch (Exception $e) {
                    // Do nothing
                }
            }
        }        
        $behaviorOutput = (count($behaviorsSettings)>0) ? sprintf('<div class="dm_behaviors none {\'behaviors\': [%s]}"></div>',
        str_replace('"', "'", implode(',', $behaviorsSettings))
        ) : '';
        
        if ($canCache && $this->isCachable()) {            
            $this->setCache($behaviorOutput);          
            // SET CACHE FOR JAVASCRIPTS AND CSS FILES AS WELL
            $this->setCache('return ' . ((count($this->getJavascripts()) > 0) ? var_export($this->getJavascripts(), true) : 'array()') .';', 'javascripts');
            $this->setCache('return ' . ((count($this->getStylesheets()) > 0) ? var_export($this->getStylesheets(), true) : 'array()') .';', 'stylesheets');
        }
        return $behaviorOutput;
    }

    private function getBehaviorsForPage($page, $areas) {
        $query = dmDb::query('DmBehavior b') // THIS IS VERY SLOW SCRIPT AND QUERY, BUT USAGE OF CACHE WILL IN VIEW MODES COMPENSATE....
                ->withI18n()
                ->where('b.dm_page_id = ?', array($page->getId()));
        if (isset($areas)) foreach ($areas as $area) {
            $query->orWhere('b.dm_area_id = ?', array($area['id']));
            if (isset($area['Zones'])) foreach($area['Zones'] as $zone) {
                $query->orWhere('b.dm_zone_id = ?', array($zone['id']));
                if (isset($zone['Widgets'])) foreach ($zone['Widgets'] as $widget) {
                    $query->orWhere('b.dm_widget_id = ?', array($widget['id']));
                }
            }
        }        
        if (!$this->adminMode) $query->andWhere ('bTranslation.dm_behavior_enabled = ?', array(true)); // IF IS NOT ADMIN MODE, LOAD ENABLED ONLY        
        $query->orderBy('b.position ASC'); // LETS HELP JAVA SCRIPT BEHAVIOR MANAGER AND PROVIDE SORTED SEQUENCE ON THE FLY
        return $query->execute();
    }   

    protected function addJavascript($keys) {        
        $this->javascripts = array_merge($this->javascripts, (array) $keys);
        return $this;
    }

    public function getJavascripts() {                
        if (!$this->adminMode && ($cache = $this->getCache('javascripts'))) return eval($cache);
        $this->addJavascript('core.behaviorsManager');
        if ($this->adminMode) {
            $this->addJavascript('core.behaviorsManagerAdmin');
            $this->addJavascript('lib.json');
        } else {
            $this->addJavascript('core.behaviorsManagerRun');
        }
        return $this->javascripts;
    }

    protected function addStylesheet($keys) {
        $this->stylesheets = array_merge($this->stylesheets, (array) $keys);
        return $this;
    }

    public function getStylesheets() {        
        if (!$this->adminMode && ($cache = $this->getCache('stylesheets'))) return eval($cache);
        //$user = $this->context->getServiceContainer()->getService('user');
        if ($this->adminMode) $this->addStylesheet ('core.behaviors');
        // Convert stylesheets array to sfWebResponse compatible
        $stylesheets = array();
        foreach($this->stylesheets as $file => $options)
        {
          if(is_int($file) && is_string($options))
          {
            $stylesheets[$options] = array();
          }
          else
          {
            $stylesheets[$file] = $options;
          }
        }
        return $stylesheets;
    }

    /**
     * Gets the list of all registered behaviors
     * @return array
     */
    public function getListOfRegisteredBehaviors() {
        return $this->behaviors;
    }
    /**
     * Gets the settings for the behavior
     * @param string $key
     * @return array 
     */
    public function getBehaviorSettings($key) {
        if (!isset($this->behaviors[$key])) throw new dmException(sprintf('There is no behavior with key "%s"', $key)); 
        else return $this->behaviors[$key]; 
    }
    /**
     * Gets the form class for the behavior
     * @param string $key
     * @return string
     */
    public function getBehaviorFormClass($key) {        
        if (isset($this->behaviors[$key]['form'])) return $this->behaviors[$key]['form'];
        else throw new dmException(sprintf('There is no behavior form class for behavior with key "%s"', $key));        
    }
    
    /**
     * Gets the view class for the behavior
     * @param string $key
     * @return string 
     */
    public function getBehaviorViewClass($key) {
        if (isset($this->behaviors[$key]['view'])) return $this->behaviors[$key]['view'];
        else throw new dmException(sprintf('There is no behavior view class for behavior with key "%s"', $key)); 
    }
    
    /**
     * Checks if the behavior is cachable...
     * @param string $key The key of the behavior
     * @return bool
     */
    public function isBehaviorCachable($key) {
        $behavior = $this->getBehaviorSettings($key);
        return $behavior['cache'];
    }
    
    /**
     * Finds a behavior from database
     * @param int $id ID of behavior
     * @return DmBehavior
     */
    public function getDmBehavior($id) {
        try {
            $behavior = dmDb::query('DmBehavior b')->withI18n()->where('b.id = ?', array($id))->fetchOne();
        } catch (Exception $e) {
            throw new dmException(sprintf('Could not fetch behavior with id = ""', $id));
        }
        if (!$behavior) throw new dmException(sprintf('Could not fetch behavior with id = ""', $id));
        return $behavior;
        
    }
    
    /**
     * Gets the ascending ordered behaviors to be sorted in some context
     * @param string $context Attached to? (page | area | zone | widget)
     * @param int $id The id of the attachable page component
     * @return array 
     */
    public function getBehaviorsForSort($context, $id) {    // TODO TheCelavi - how to sort in content behaviors? How to only fetch them?
        $behaviors = array();
        switch ($context) {
            case 'page': {
                    $behaviors = $this->getPageBehaviors($id);
            } break;
            case 'area': {
                    $behaviors = $this->getAreaBehaviors($id);
            } break;
            case 'zone': {
                    $behaviors = $this->getZoneBehaviors($id);
            } break;
            case 'widget': {
                    $behaviors = $this->getWidgetBehaviors($id);
            } break;
        }
        if (count($behaviors) == 0) throw new dmException (sprintf('There are no behaviors to be sorted in this %s', $context));
        usort($behaviors, array('dmBehaviorsManager', 'sortBehaviors'));
        return $behaviors;
    }

    static function sortBehaviors($a, $b) {
        return ($a['position'] - $b['position'] > 0);
    }


    private function getPageBehaviors($page) {
        if (!$page instanceof DmPage) $page = dmDb::query('DmPage p')->where('p.id = ?', array($page))->fetchOne();               
        $behaviors = ($page->getBehaviors()) ? $page->getBehaviors()->toArray() : array();
        $pageView = dmDb::query('DmPageView pv')->where('pv.module = ?', array($page->getModule()))->andWhere('pv.action = ?', array($page->getAction()))->fetchOne();        
        $areas = $pageView->getAreas();
        foreach ($areas as $area) {
            $behaviors = array_merge($behaviors, $this->getAreaBehaviors($area));
        }
        return $behaviors;
    }
    private function getAreaBehaviors($area) {
        if (!$area instanceof DmArea) $area = dmDb::query('DmArea a')->where('a.id = ?', array($area))->fetchOne();
        $behaviors = ($area->getBehaviors()) ? $area->getBehaviors()->toArray() : array();
        foreach ($area->getZones() as $zone) {
            $behaviors = array_merge($behaviors, $this->getZoneBehaviors($zone));
        }
        return $behaviors;
    }
    private function getZoneBehaviors($zone) {
        if (!$zone instanceof DmZone) $zone = dmDb::query('DmZone z')->where('z.id = ?', array($zone))->fetchOne();
        $behaviors = ($zone->getBehaviors()) ? $zone->getBehaviors()->toArray() : array();
        foreach ($zone->getWidgets() as $widget) {
            $behaviors = array_merge($behaviors, $this->getWidgetBehaviors($widget));
        }
        return $behaviors;
    }
    private function getWidgetBehaviors($widget) {
        if (!$widget instanceof DmWidget) $widget = dmDb::query('DmWidget w')->withI18n()->where('w.id = ?', array($widget))->fetchOne();
        return ($widget->getBehaviors()) ? $widget->getBehaviors()->toArray() : array();
    }
    

    /*******************************************
     *          UTILITY FUNCTIONS
     ******************************************/
    
    /**
     * Gets the array of the behaviors, sorted by sections
     * This should be used only for menu build up...
     * @return nested array
     */
    public function getBehaviorsMenuItems() {
        return sfConfig::get('dm_behaviors_menu_items');
    }
    
    protected function getService($name, $class = null) {
        return $this->context->get($name, $class);
    }

    /*******************************************
     *          CACHING FUNCTIONS
     ******************************************/
    public function isCachable() {
        return sfConfig::get('sf_cache');
    }

    public function getCache($type = 'output') {
        return $this->getService('cache_manager')->getCache($this->getCacheName())->get($this->generateCacheKey($type));
    }

    public function setCache($settings, $type = 'output') {
        return $this->getService('cache_manager')->getCache($this->getCacheName())->set($this->generateCacheKey($type), $settings, 86400);
    }

    protected function getCacheName() {
        return sprintf('%s/%s/template', sfConfig::get('sf_app'), sfConfig::get('sf_environment'));
    }

    protected function generateCacheKey($type) {
        return sprintf(
                        'behaviors_manager/%s/%s/%s', $type, $this->page['id'], md5(serialize($this->filterCacheVars(array(
                            
                        ))))
        );
    }

    protected function filterCacheVars(array $vars) {
        if ($this->page) {
            $vars['page_id'] = $this->page['id'];
            $vars['user_id'] = $this->context->getServiceContainer()->getService('user')->getUserId();
            $vars['culture'] = $this->context->getServiceContainer()->getService('user')->getCulture();
        }
        return $vars;
    }
    
}