<?php

class dmWidgetContentLinkForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->widgetSchema['href']     = new sfWidgetFormInputText(array(), array(
      'class' => 'dm_link_droppable',
      'title' => $this->__('Accepts pages, medias and urls')
    ));
    
    $this->validatorSchema['href']  = new dmValidatorLinkUrl(array('required' => true));
    
    $this->widgetSchema['text']     = new sfWidgetFormTextarea(array(), array('rows' => 2));
    $this->validatorSchema['text']  = new sfValidatorString(array('required' => false));

    $this->widgetSchema['title']    = new sfWidgetFormInputText();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));

    parent::configure();
  }

  public function renderContent($attributes) {
        $formRenderer = new dmFrontFormRenderer(array(
            new dmFrontFormSection(
                    array(
                        array("name"=>'href', "is_big"=>true),
                        array("name"=>'text', "is_big"=>true),
                        array("name"=>'title', "is_big"=>true),
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