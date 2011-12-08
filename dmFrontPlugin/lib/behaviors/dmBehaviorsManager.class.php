<?php

/**
 * Service for managing behaviors
 *
 * @author TheCelavi
 */
class dmBehaviorsManager {
    
    public $behaviors = array();

    public function __construct() {
        sfContext::getInstance()->getConfigCache()->registerConfigHandler('config/dm/behaviors.yml', 'dmBehaviorsConfigHandler', array());
        include sfContext::getInstance()->getConfigCache()->checkConfig('config/dm/behaviors.yml');
    }

    public function renderBehaviorsToolbar() {              
        $helper = dmContext::getInstance()->getHelper();
        return $helper->renderPartial('dmBehaviorsFramework', 'add_behaviors_menu', array(
            'behaviors' => sfConfig::get('dm_behaviors')
        ));
    }
    
    public function getBehaviorSettings($behaviorID) {
        $behaviors = sfConfig::get('dm_behaviors');
        $result = null;
        $sectionName = null;
        foreach ($behaviors as $section) {
            $sectionName = $section['section_name'];
            foreach ($section['behaviors'] as $key=>$behavior) {
                if ($key == $behaviorID) {
                    $result = $behavior;
                    break;
                }
                if (!is_null($result)) break;
            }
            if (!is_null($result)) break;
        }
        if (is_null($result)) return null; // TODO THROW USER ERROR HERE
        else return array(
            'dmBehaviorSection'     =>      $sectionName,
            'dmBehaviorKey'         =>      $behaviorID,
            'dmBehaviorName'        =>      $result['name'],
            'dmBehaviorIcon'        =>      $result['icon'],
            'dmBehaviorForm'        =>      $result['form'],
            'dmBehaviorView'        =>      $result['view'],
        );
    }
    
    public function registerBehaviors($widgetBehaviors) {
        if ($widgetBehaviors == "") return;
        $behaviors = json_decode($widgetBehaviors, true);        
        return $this->extractBehaviorsSettings($behaviors);
    }
    
    protected function extractBehaviorsSettings($behaviors) {
        $result = array();        
        foreach ($behaviors as $behavior) {
            unset ($behavior['dmBehaviorTempID']);
            if (!isset ($this->behaviors[$behavior['dmBehaviorKey']])) {
                $behavior = array_merge($behavior, $this->getBehaviorSettings($behavior['dmBehaviorKey']));
                $this->behaviors[$behavior['dmBehaviorKey']] = new $behavior['dmBehaviorView']();
            }
            $result[$behavior['dmBehaviorKey']][] = $this->behaviors[$behavior['dmBehaviorKey']]->addSettings($behavior);
        }
        return $result;
    }    
    
    public function getJavascripts() {
        $js = array();
        foreach ($this->behaviors as $behavior) {
            $js = array_merge($js, $behavior->getJavascripts());
        }
        return $js;
    }
    
    public function getStylesheets() {
        $css = array();
        foreach ($this->behaviors as $behavior) {
            $css = array_merge($css, $behavior->getStylesheets());            
        }
        return $css;
    }
    
    public function renderBehaviorHtmlMetadata($data) {
        return str_replace('"', "'", json_encode($data));
    }
}