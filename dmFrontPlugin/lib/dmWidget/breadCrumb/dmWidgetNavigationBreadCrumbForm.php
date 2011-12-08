<?php

class dmWidgetNavigationBreadCrumbForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->widgetSchema['separator'] = new sfWidgetFormInputText();
    $this->widgetSchema['includeCurrent'] = new sfWidgetFormInputCheckBox();
    $this->widgetSchema['includeInactivePages'] = new sfWidgetFormInputCheckBox();

    $this->validatorSchema['separator'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $this->validatorSchema['includeCurrent']  = new sfValidatorBoolean();
    $this->validatorSchema['includeInactivePages']  = new sfValidatorBoolean();
        
    $this->widgetSchema['includeCurrent']->setLabel('Include current page');
    $this->widgetSchema['includeInactivePages']->setLabel('Include inactive pages');

    $this->setDefaults($this->getDefaultsFromLastUpdated(array('separator', 'includeCurrent')));

    parent::configure();
  }

  protected function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'separator'      => '>',
      'includeCurrent' => true,
      'includeInactivePages' => true
    ));
  }
  
  public function renderContent($attributes) {
        $formRenderer = new dmFrontFormRenderer(array(
            new dmFrontFormSection(
                    array(
                        array("name"=>'separator', "is_big"=>true),
                        'includeCurrent',
                        'includeInactivePages'
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