<?php

class dmWidgetSearchFormForm extends dmWidgetPluginForm
{

  public function configure()
  {
    parent::configure();
    
    dmDb::table('DmPage')->checkSearchPage();
  }

  public function renderContent($attributes) {
        $formRenderer = new dmFrontFormRenderer(array(                          
            new dmFrontFormSection(
                    array(
                        array("name"=>'behaviors', "is_big"=>true),                       
                        array("name"=>'cssClass', "is_big"=>true),
                        ),
                    'Advanced'
                    )
            
            
        ), $this);
        return $formRenderer->render();        
    }
    
    public function getStylesheets() {
        return array_merge(
            parent::getStylesheets(),
            dmFrontFormRenderer::getStylesheets()
        );
    }
    public function getJavaScripts() {
        return array_merge(
            parent::getJavaScripts(),
            dmFrontFormRenderer::getJavascripts()
        );
    }
  
}