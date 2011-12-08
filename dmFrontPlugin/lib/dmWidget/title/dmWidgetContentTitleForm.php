<?php

class dmWidgetContentTitleForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->widgetSchema['text'] = new sfWidgetFormTextarea(array(), array(
      'rows' => 2
    ));
    $this->widgetSchema['tag']  = new sfWidgetFormChoice(array('choices' => $this->getTagNames()));
    
    $this->widgetSchema['href'] = new sfWidgetFormInputText(array(), array(
      'class' => 'dm_link_droppable',
      'title' => $this->__('Accepts pages, medias and urls')
    ));
    $this->widgetSchema->setHelp('href', 'If you set a href, a link will be inserted into the title');

    $this->validatorSchema['text'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['tag']  = new sfValidatorChoice(array('choices' => $this->getTagNames(), 'required' => true));
    $this->validatorSchema['href'] = new dmValidatorLinkUrl(array('required' => false));

    parent::configure();
  }

  protected function getTagNames()
  {
    return dmArray::valueToKey(array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div'));
  }
  
  public function renderContent($attributes) {
        $formRenderer = new dmFrontFormRenderer(array(                          
            new dmFrontFormSection(
                    array(
                        array("name"=>'text', "is_big"=>true),                       
                        array("name"=>'tag', "is_big"=>true),
                        array("name"=>'href', "is_big"=>true),
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