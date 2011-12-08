<?php
/**
 * Description of dmFrontFormRendererPlugin
 *
 * @author TheCelavi
 */
class dmFrontFormRenderer {
    
    public static $TWO_COLUMNS_TABULAR = "twoColumnsTabular";

    protected $sections;
    protected $form;


    public function __construct(array $sections, $form) {
        $this->sections = $sections;
        $this->form = $form;
    }
    
    public function render($layout = "") {
        $sections = $this->removeNonExisting($this->sections, $this->form);
        $helper = dm::getHelper();
        if ($layout == "") $layout = dmFrontFormRenderer::$TWO_COLUMNS_TABULAR;        
        return $helper->renderPartial('dmFrontFormRenderer', $layout, array(
            'sections'=>$this->sections, 
            'form'=>$this->form
                ));  
    }
    
    public static function getJavascripts($layout = "") {
        if ($layout == "") $layout = dmFrontFormRenderer::$TWO_COLUMNS_TABULAR;
        switch ($layout) {
            case dmFrontFormRenderer::$TWO_COLUMNS_TABULAR: {
                return array(
                    'lib.ui-tabs',
                    'core.tabForm',
                    '/dmFrontPlugin/js/frontFormRenderer/twoColumnsTabular.js'
                );
            } break;
        }
    }
    
    public static function getStylesheets($layout = "") {
        if ($layout == "") $layout = dmFrontFormRenderer::$TWO_COLUMNS_TABULAR;
        switch ($layout) {
            case dmFrontFormRenderer::$TWO_COLUMNS_TABULAR: {
                return array(
                    'lib.ui',
                    'lib.ui-tabs',
                    '/dmFrontPlugin/css/frontFormRenderer/dmFrontFormRenderer.css',
                    '/dmFrontPlugin/css/frontFormRenderer/twoColumnsTabular.css'
                );
            } break;            
        }
    }
    
    protected function removeNonExisting(array $sections, $form) {
        // If fields are for some reason removed from the form via "unset" or anything else
        $compiled = array();
        foreach ($sections as $section) {
            $compiled[] = $section->removeNonExisting($form);
        }
        $remove = array();
        for ($i=0; $i<count($compiled); $i++) {
            if ($compiled[$i]->isWithoutFields()) $remove[] = $i;
        }
        $remove = array_reverse($remove);        
        foreach ($remove as $index) unset ($compiled[$index]);
        return $compiled;
    }
}