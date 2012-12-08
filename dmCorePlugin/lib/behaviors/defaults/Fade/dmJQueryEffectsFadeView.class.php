<?php

class dmJQueryEffectsFadeView extends dmBehaviorBaseView {
    
    public function configure() {
        $this->addRequiredVar(array('event', 'opacity', 'duration', 'easing'));
    }

    protected function filterBehaviorVars(array $vars = array()) {
        $vars = parent::filterBehaviorVars($vars);
        $vars['opacity'] = (isset($vars['opacity'])) ? round($vars['opacity']/100, 2) : 0.50;
        return $vars;
    }
    
    public function getJavascripts() {
        return array_merge(
            parent::getJavascripts(),            
            array(
                'lib.ui-core',
                'lib.ui-effects-core',
                'lib.easing',
                'core.jQueryEffectsFadeBehavior'
            )
        );
    }    
}

