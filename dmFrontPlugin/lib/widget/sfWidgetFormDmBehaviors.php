<?php
/**
 * sfWidgetFormField for attaching the behaviors
 *
 * @author TheCelavi
 */
class sfWidgetFormDmBehaviors extends sfWidgetFormInputText {
    
    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        return 
            '<div class="dm_widget_form_behaviors_droppable">
                <span style="display: none">' 
                    . parent::render($name, $value, $attributes, $errors) . 
                '</span>
             </div>';
    }
    
    public function getJavaScripts() {        
        return array_merge(parent::getJavaScripts(), array(
            'lib.json',
            'front.behaviorsFormField'
        ));
    }

    public function getStylesheets() {
        return array_merge(parent::getStylesheets(), array(
            'front.behaviorsFormField'
        ));
    }
    
}