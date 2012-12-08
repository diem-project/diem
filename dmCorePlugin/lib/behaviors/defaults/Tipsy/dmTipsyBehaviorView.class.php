<?php
/**
 * @author TheCelavi
 */
class dmTipsyBehaviorView extends dmBehaviorBaseView {
    
    public function configure() {
        $this->addRequiredVar(array('title', 'gravity', 'opacity', 'fade', 'delayIn', 'delayOut'));
    }

    protected function filterBehaviorVars(array $vars = array()) {
        $vars = parent::filterBehaviorVars($vars);
        if (isset($vars['inner_target']) && trim($vars['inner_target']) != '') $vars['inner_target'] = explode(',', $vars['inner_target']);
        else $vars['inner_target'] = false;
        $vars['opacity'] = (isset($vars['opacity'])) ? round($vars['opacity']/100, 2) : 0.80;
        return $vars;
    }
    
    public function getJavascripts() {
        return array_merge(
            parent::getJavascripts(),            
            array(
                'lib.tipsy',
                'core.dmTipsyBehavior'
            )
        );
    } 
    
    public function getStylesheets() {
        return array_merge(
            parent::getStylesheets(),
            array(
                'lib.tipsy'
            )
        );
    }
    
}

