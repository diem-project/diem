<?php

class dmAdminPageMetaView
{
  protected
  $helper,
  $i18n,
  $page,
  $availableFields = array(
    'lft' => 'Position',
    'name' => 'Name',
    'slug' => 'Url',
    'title' => 'Title',
    'h1' => 'H1',
    'description' => 'Description',
    'keywords' => 'Keywords',
    'is_active' => 'Available',
    'is_secure' => 'Secure',
    'is_indexable' => 'Indexable'
  );

  public function __construct(dmHelper $helper, dmI18n $i18n)
  {
    $this->helper = $helper;
    $this->i18n   = $i18n;

    $this->initialize();
  }

  protected function initialize()
  {
    $this->clickToEditText = $this->i18n->__('Click to edit');
  }

  public function setPage($page)
  {
    $this->page = $page;
  }

  public function getAvailableFields()
  {
    return array_keys($this->availableFields);
  }

  public function renderField($field)
  {
    return $this->i18n->__($this->availableFields[$field]);
  }

  public function renderMeta($field)
  {
    if(0 === strncmp($field, 'is_', 3))
    {
      $html = $this->renderBooleanMeta($field);
    }
    else
    {
      $html = $this->renderStringMeta($field);
    }

    return $html;
  }

  public function renderStringMeta($field)
  {
    $value = strip_tags($this->page[$field]);
    
    if('lft' === $field)
    {
      return sprintf(
        '<td><span style="margin-left: %dpx;" class="s16block s16_page_%s">%s</span></td>',
        $this->page['level'] * 15,
        'show' === $this->page['action'] ? 'auto' : 'manual',
        $value
      );
    }
    elseif('slug' === $field && 1 == $this->page['lft'])
    {
      return '<td>/</td>';
    }
    
    $editType = in_array($field, array('description', 'keywords')) ? 'edit_textarea' : 'edit_input';
    
    return sprintf(
      '<td class="editable %s" rel="%s" title="%s">%s</td>',
      $editType,
      $field,
      $this->clickToEditText,
      $value
    );
  }

  public function renderBooleanMeta($field)
  {
    return sprintf(
      '<td><span class="boolean s16block s16_%s" rel="%s" title="%s"></span></td>',
      $this->page[$field] ? 'tick' : 'cross',
      $field,
      $this->clickToEditText
    );
  }
}