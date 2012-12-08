<?php

/**
 * The class in charge for the filtering, caching and rendering of the behaviors
 * as well as loading required javascripts and stylesheets
 *
 * @author TheCelavi
 */
abstract class dmBehaviorBaseView {

    protected
    $context,
    $behavior,
    $requiredVars = array(),
    $vars,
    $javascripts = array(),
    $stylesheets = array();

    public function __construct(dmContext $context, DmBehavior $behavior) {
        $this->context = $context;
        $this->behavior = $behavior;
        $this->configure();
    }

    protected function configure() {
        
    }

    protected function addJavascript($keys) {
        $this->javascripts = array_merge($this->javascripts, (array) $keys);
        return $this;
    }

    public function getJavascripts() {
        return $this->javascripts;
    }

    protected function addStylesheet($keys) {
        $this->stylesheets = array_merge($this->stylesheets, (array) $keys);
        return $this;
    }

    public function getStylesheets() {
        return $this->stylesheets;
    }

    public function getRequiredVars() {
        return $this->requiredVars;
    }

    public function isRequiredVar($var) {
        return in_array($var, $this->getRequiredVars());
    }

    public function addRequiredVar($var) {
        if (is_array($var)) {
            $this->requiredVars = array_merge($this->requiredVars, $var);
        } else {
            $this->requiredVars[] = $var;
        }
        $this->requiredVars = array_unique($this->requiredVars);
    }

    public function removeRequiredVar($var) {
        if (is_array($var)) {
            foreach ($var as $v) {
                $this->removeRequiredVar($v);
            }
        } elseif (false !== ($varIndex = array_search($var, $this->requiredVars))) {
            unset($this->requiredVars[$varIndex]);
        }
    }

    protected function compileVars(array $vars = array()) {
        $this->compiledVars = array_merge(
                array(), 
                (array) json_decode((string) $this->behavior['dm_behavior_value'], true),
                array(
                    'dm_behavior_id'                    =>  $this->behavior['id'],
                    'dm_behavior_key'                   =>  $this->behavior['dm_behavior_key'],                    
                    'dm_behavior_attached_to'           =>  $this->behavior['dm_behavior_attached_to'],
                    'dm_behavior_attached_to_id'        =>  (int) $this->behavior['dm_page_id'] + $this->behavior['dm_area_id'] + $this->behavior['dm_zone_id'] + $this->behavior['dm_widget_id'],
                    'dm_behavior_attached_to_content'   =>  ($this->behavior['dm_behavior_attached_to_selector'] != '') ? true : false,
                    'dm_behavior_attached_to_selector'  =>  ($this->behavior['dm_behavior_attached_to_selector'] != '') ? $this->behavior['dm_behavior_attached_to_selector'] : null,
                    'dm_behavior_sequence'              =>  $this->behavior['position'],
                    'dm_behavior_enabled'               =>  $this->isEnabled(),
                    'dm_behavior_valid'                 =>  true
                ),
                dmString::toArray($vars)
        );
    }

    protected function doRender() {
        if ($this->isCachable() && $cache = $this->getCache()) {
            return $cache;
        }
        $json = json_encode($this->getBehaviorVars());
        if ($this->isCachable()) {
            $this->setCache($json);
        }
        return $json;
    }

    public function renderDefault() {
        if ($this->context->getUser()->can('behavior_edit') || $this->context->getUser()->can('behavior_delete') || $this->context->getUser()->can('behavior_add')) {
            $json = json_encode(array(
                'dm_behavior_id'                    =>  $this->behavior['id'],
                'dm_behavior_key'                   =>  $this->behavior['dm_behavior_key'],                    
                'dm_behavior_attached_to'           =>  $this->behavior['dm_behavior_attached_to'],
                'dm_behavior_attached_to_id'        =>  (int) $this->behavior['dm_page_id'] + $this->behavior['dm_area_id'] + $this->behavior['dm_zone_id'] + $this->behavior['dm_widget_id'],
                'dm_behavior_attached_to_content'   =>  ($this->behavior['dm_behavior_attached_to_selector'] != '') ? true : false,
                'dm_behavior_attached_to_selector'  =>  ($this->behavior['dm_behavior_attached_to_selector'] != '') ? $this->behavior['dm_behavior_attached_to_selector'] : null,
                'dm_behavior_sequence'              =>  $this->behavior['position'],
                'dm_behavior_enabled'               =>  $this->isEnabled(),
                'dm_behavior_valid'                 =>  $this->isValid()
            ));
        } else {
            $json = null;
        }
        return $json;
    }
    
    public function render(array $vars = array()) {
        $this->compileVars($vars);
        if ($this->isValid() && $this->isEnabled()) {
            $json = $this->doRender();
        } else {
            $json = $this->renderDefault();
        }
        return $json;
    }    

    public function renderArray() {
        $this->compileVars();
        return $this->getBehaviorVars();
    }
    
    protected function isValid() {
        foreach ($this->getRequiredVars() as $requiredVar) {
            if (!isset($this->compiledVars[$requiredVar])) {
                return false;
            }
        }
        return true;
    }

    protected function isEnabled() {
        return (bool) $this->behavior['dm_behavior_enabled'];
    }

    public function getBehaviorVars() {
        if (!is_array($this->compiledVars)) {
            throw new dmException('Behavior view vars have not been compiled yet');
        }
        return $this->filterBehaviorVars($this->compiledVars);
    }

    protected function filterBehaviorVars(array $vars = array()) {
        return $vars;
    }

    public function isCachable() {
        return sfConfig::get('sf_cache') && $this->getService('behaviors_manager')->isBehaviorCachable($this->behavior['dm_behavior_key']);
    }

    public function getCache() {
        return $this->getService('cache_manager')->getCache($this->getCacheName())->get($this->generateCacheKey());
    }

    public function setCache($json) {
        return $this->getService('cache_manager')->getCache($this->getCacheName())->set($this->generateCacheKey(), $json, 86400);
    }

    protected function getCacheName() {
        return sprintf('%s/%s/template', sfConfig::get('sf_app'), sfConfig::get('sf_environment'));
    }

    protected function generateCacheKey() {
        return sprintf(
                        'behavior/%s/%s/%s', $this->behavior['dm_behavior_key'], $this->behavior['id'], md5(serialize($this->filterCacheVars($this->compiledVars)))
        );
    }

    protected function filterCacheVars(array $vars) {
        if ($this->context->getPage()) {
            $vars['page_id'] = $this->context->getPage()->get('id');
            $vars['user_id'] = $this->getService('user')->getUserId();
            $vars['culture'] = $this->getService('user')->getCulture();
        }
        return $vars;
    }

    protected function getHelper() {
        return $this->context->getHelper();
    }

    protected function getService($name, $class = null) {
        return $this->context->get($name, $class);
    }

    protected function __($message, $arguments = array(), $catalogue = null) {
        return $this->context->getI18n()->__($message, $arguments, $catalogue);
    }

}

