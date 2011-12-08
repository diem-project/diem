<?php

class dmWidgetSearchResultsForm extends dmWidgetPluginForm
{

  public function configure()
  {
    parent::configure();

    /// Max per page
    $this->widgetSchema['maxPerPage']     = new sfWidgetFormInputText(array(
      'label' => 'Max results per page'
    ), array(
      'size' => 3
    ));
    $this->validatorSchema['maxPerPage']  = new sfValidatorInteger(array(
      'min' => 0,
      'max' => 99999,
      'required' => false
    ));

    // Paginators top & bottom
    $this->widgetSchema['navTop']       = new sfWidgetFormInputCheckbox(array(
      'label' => 'Show navigation top'
    ));
    $this->validatorSchema['navTop']    = new sfValidatorBoolean();

    $this->widgetSchema['navBottom']    = new sfWidgetFormInputCheckbox(array(
      'label' => 'Show navigation bottom'
    ));
    $this->validatorSchema['navBottom'] = new sfValidatorBoolean();
  }

  protected function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'maxPerPage' => 10
    ));
  }
  
  public function renderContent($attributes) {
        $formRenderer = new dmFrontFormRenderer(array(
            new dmFrontFormSection(
                    array(
                        array("name"=>'maxPerPage', "is_big"=>false),
                        array("name"=>'empty', 'type'=>'empty'),
                        array("name"=>'navTop', "is_big"=>false),
                        array("name"=>'navBottom', "is_big"=>false),
                        ),
                    'Basic'
                    ),               
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