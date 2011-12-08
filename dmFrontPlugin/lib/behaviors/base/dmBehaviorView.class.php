<?php

/**
 * Description of dmBehaviorView
 *
 * @author TheCelavi
 */
abstract class dmBehaviorView {

    protected
    $context,
    $settings,
    $javascripts,
    $stylesheets;

    public function __construct() {        
        $this->settings = array();
        $this->javascripts = array();
        $this->stylesheets = array();
    }
    
    public function addSettings($settings) {
        $compiled = $this->filterSettings($settings);
        $this->settings[] = $compiled;
        return $compiled;
    }
    
    public function getSettings() {
        return $this->getSettings();
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
    
    protected function addJavascript($keys) {
        $this->javascripts = array_merge($this->javascripts, (array) $keys);
        return $this;
    }
    
    protected function addStylesheet($keys) {
        $this->stylesheets = array_merge($this->stylesheets, (array) $keys);
        return $this;
    }
    
    // Override this functions to get JavaScripts, CSS files and settings as your behavior need
    
    public function getJavascripts() {
        return $this->javascripts;
    }

    public function getStylesheets() {
        return $this->stylesheets;
    }

    protected function filterSettings($settings) {
        return $settings;
    }
    
}

