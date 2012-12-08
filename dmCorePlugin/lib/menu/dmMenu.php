<?php

class dmMenu extends dmConfigurable implements ArrayAccess, Countable, IteratorAggregate
{
  protected
  $serviceContainer,
  $helper,
  $user,
  $i18n,
  $name,
  $label,
  $link,
  $level,
  $num,
  $parent,
  $secure,
  $credentials  = array(),
  $children     = array();

  public function __construct(dmBaseServiceContainer $serviceContainer, $options = array())
  {
    $this->serviceContainer = $serviceContainer;

    $this->helper     = $serviceContainer->getService('helper');
    $this->user       = $serviceContainer->getService('user');
    $this->i18n       = $serviceContainer->getService('i18n');

    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function getDefaultOptions()
  {
    return array(
      'ul_class'          => null,
      'li_class'          => null,
      'show_id'           => false,
      'show_children'     => true,
      'translate'         => true
    );
  }

  /**
   * Setters
   */

  public function name($name = null)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Setter for the label.
   *
   * You can use this setter to override the default label generated
   * A label is generated when there is no link passed to the addChild method
   * or when link is set to false with link(false)
   * For example, it's easy to add a span around the label with
   * label('<span class="you_class">Menu label</span>')
   *
   * @see link()
   * @see addChild()
   *
   * @param  string $label The label to render
   *
   * @return object Return $this (fluent interface)
   */
  public function label($label)
  {
    $this->label = $label;

    return $this;
  }

  public function link($link)
  {
    $this->link = $link;

    return $this;
  }

  public function secure($bool)
  {
    return $this->setOption('secure', (bool) $bool);
  }

  public function notAuthenticated($bool)
  {
    return $this->setOption('not_authenticated', (bool) $bool);
  }

  public function translate($bool)
  {
    return $this->setOption('translate', (bool) $bool);
  }

  public function credentials($credentials)
  {
    return $this->setOption('credentials', $credentials);
  }
  
  public function ulClass($class)
  {
    return $this->setOption('ul_class', $class);
  }

  public function liClass($class)
  {
    return $this->setOption('li_class', $class);
  }

  /**
   * Display ID html attributes ?
   */
  public function showId($bool)
  {
    return $this->setOption('show_id', $bool);
  }

  public function showChildren($bool)
  {
    return $this->setOption('show_children', $bool);
  }

  public function level($level = null)
  {
    $this->level = $level;

    return $this;
  }

  public function parent(dmMenu $parent)
  {
    $this->parent = $parent;

    return $this;
  }

  public function children(array $children = null)
  {
    $this->children = $children;

    return $this;
  }

  public function num($num = null)
  {
    $this->num = $num;

    return $this;
  }

  /**
   * Getters
   */

  public function getName()
  {
    return $this->name;
  }

  public function getLabel()
  {
    return null === $this->label ? $this->getName() : $this->label;
  }

  /**
   * @return dmBaseLinkTag the link tag
   */
  public function getLink()
  {
    if(!$this->link instanceof dmBaseLinkTag && !empty($this->link))
    {
      $this->link = $this->helper->link($this->link);
    }
    
    return $this->link;
  }

  /**
   * @return dmMenu the parent menu
   */
  public function end()
  {
    return $this->parent;
  }


  /**
   * Returns the nesting level
   *
   * Use it when you want to override some of the default behavior
   * depending on the nesting level of your menu.
   * You could easily add some html to the link or the label
   * depending of the level by overriding renderLabel or renderLink
   *
   * @see renderLabel()
   * @see renderLink()
   *
   * @return int true Nesting level of the menu item, based on the nested set
   */
  public function getLevel()
  {
    if (null === $this->level)
    {
      $this->level = $this->parent ? $this->parent->getLevel() + 1 : -1;
    }

    return $this->level;
  }

  /**
   * @return dmMenu the root menu
   */
  public function getRoot()
  {
    return $this->parent ? $this->parent->getRoot() : $this;
  }

  /**
   * @return dmMenu the parent menu
   */
  public function getParent()
  {
    return $this->parent;
  }

  public function getChildren()
  {
    return $this->children;
  }

  public function getNbChildren()
  {
    return count($this->getChildren());
  }
  
  public function getNum($num = null)
  {
    return $this->num;
  }

  public function getFirstChild()
  {
    return dmArray::first($this->children);
  }

  public function getLastChild()
  {
    return dmArray::last($this->children);
  }

  public function getChild($name)
  {
    if (!$this->hasChild($name))
    {
      $this->addChild($name);
    }

    return $this->children[$name];
  }

  public function getSiblings($includeMe = false)
  {
    if(!$this->getParent())
    {
      throw new dmException('Root menu has no siblings');
    }

    $siblings = array();
    foreach($this->getParent()->getChildren() as $key => $child)
    {
      if($includeMe || $child != $this)
      {
        $siblings[$key] = $child;
      }
    }

    return $siblings;
  }

  /**
   * Returns true if the user has access to this menu item.
   *
   * false if the user is authenticated and the menu is only for
   * not authenticated users
   *
   * true is the menu is not secure and there is no credential on it
   *
   * false if the module is secure and the user not authenticated
   *
   * false if the user has not the correct credentials
   *
   *
   * @return boolean true if the user has access to this menu item or not
   */
  public function checkUserAccess()
  {
    if (!empty($this->options['not_authenticated']) && $this->user->isAuthenticated())
    {
      return false;
    }

    if(empty($this->options['secure']) && empty($this->options['credentials']))
    {
      return true;
    }

    if (!empty($this->options['secure']) && !$this->user->isAuthenticated())
    {
      return false;
    }

    return $this->user->can($this->getOption('credentials'));
  }

  public function hasChildren()
  {
    $nbChildren = 0;

    foreach ($this->children as $child)
    {
      $nbChildren += (int) $child->checkUserAccess();
    }

    return 0 !== $nbChildren;
  }
  
  /**
   * Returns true if the item has a child with the given name.
   *
   * @see addChild()
   *
   * @param  string $iname The child name
   *
   * @return boolean true if the item has a child with the given name
   *
   */
  public function hasChild($name)
  {
    return isset($this->children[$name]);
  }

  /**
   * Move
   */
  public function moveToFirst()
  {
    $siblings = array($this->getName() => $this) + $this->getSiblings();

    $this->getParent()->setChildren($siblings)->reAssignChildrenNums();

    return $this;
  }

  public function moveToLast()
  {
    $siblings = $this->getSiblings() + array($this->getName() => $this);

    $this->getParent()->setChildren($siblings)->reAssignChildrenNums();

    return $this;
  }

  public function reAssignChildrenNums()
  {
    $it = 0;
    foreach($this->getChildren() as $child)
    {
      $child->num(++$it);
    }
  }

  /**
   * Manipulation
   */

  public function addChild($child, $link = null)
  {
    if (!$child instanceof dmMenu)
    {
      $child = $this->serviceContainer->getService('menu', get_class($this))->name($child);
    }

    // don't increment num if the child already exists and is overriden
    // but use the old child num
    $num = $this->hasChild($child->getName()) ? $this->getChild($child->getName())->getNum() : $this->count()+1;

    return $this->children[$child->getName()] = $child
    ->link($link)
    ->num($num)
    ->parent($this);
  }

  public function removeChild($child)
  {
    if($child instanceof dmMenu)
    {
      $child = $child->getName();
    }
    
    unset($this->children[$child]);

    return $this;
  }

  public function removeChildren()
  {
    $this->children = array();

    return $this;
  }

  public function setChildren(array $children)
  {
    $this->children = $children;

    return $this;
  }

  /**
   * Recursively add children based on the page structure
   */
  public function addRecursiveChildren($depth = 1)
  {
    if($depth < 1 || !$this->getLink() instanceof dmFrontLinkTagPage || !$this->getLink()->getPage()->getNode()->hasChildren())
    {
      return $this;
    }

    $treeObject = dmDb::table('DmPage')->getTree();
    $treeObject->setBaseQuery(
      dmDb::table('DmPage')->createQuery('p')
      ->withI18n($this->user->getCulture(), null, 'p')
      ->select('p.*, pTranslation.*')
    );

    if($pageChildren = $this->getLink()->getPage()->getNode()->getChildren())
    {
      foreach($pageChildren as $childPage)
      {
        $this->addChild($childPage->get('name'), $childPage)->addRecursiveChildren($depth - 1);
      }
    }

    $treeObject->resetBaseQuery();

    return $this;
  }

  /**
   * Rendering
   */

  public function __toString()
  {
    try
    {
      return (string) $this->render();
    }
    catch (Exception $e)
    {
      return $e->getMessage();
    }
  }

  public function render()
  {
    $html = '';

    if ($this->checkUserAccess() && $this->hasChildren())
    {
      $html = $this->renderUlOpenTag();

      foreach ($this->children as $child)
      {
        $html .= $child->renderChild();
      }

      $html .= '</ul>';
    }

    return $html;
  }

  protected function renderUlOpenTag()
  {
    $class  = $this->getOption('ul_class');
    $id     = $this->getOption('show_id') ? dmString::slugify($this->name.'-menu') : null;
    
    return '<ul'.($id ? ' id="'.$id.'"' : '').($class ? ' class="'.$class.' dm_menu_ul_level_'.($this->getLevel()+1).'"' : ' class="dm_menu_ul_level_'.($this->getLevel()+1).'"').'>';
    
    //return '<ul'.($id ? ' id="'.$id.'"' : '').($class ? ' class="'.$class.'"' : '').'>';
  }

  public function renderChild()
  {
    $html = '';
    
    if ($this->checkUserAccess())
    {
      $html .= $this->renderLiOpenTag();

      $html .= $this->renderChildBody();

      if ($this->hasChildren() && $this->getOption('show_children'))
      {
        $html .= $this->render();
      }

      $html .= '</li>';
    }

    return $html;
  }

  protected function renderLiOpenTag()
  {
    $classes  = array();
    $id       = $this->getOption('show_id') ? dmString::slugify('menu-'.$this->getRoot()->getName().'-'.$this->getName()) : null;
    $link     = $this->getLink();

    if ($this->isFirst())
    {
      $classes[] = 'first';
    }
    if ($this->isLast())
    {
      $classes[] = 'last';
    }
    if ($this->getOption('li_class'))
    {
      $classes[] = $this->getOption('li_class');
    }
    if($link && $link->isCurrent())
    {
      $classes[] = $link->getOption('current_class');
    }
    elseif($link && $link->isParent())
    {
      $classes[] = $link->getOption('parent_class');
    }
    
    $classes[] = 'dm_menu_ul_level_'.($this->getLevel()+1).'_li_item_'.$this->num;
    
    return '<li'.($id ? ' id="'.$id.'"' : '').' class="'.implode(' ', $classes).'">';
    
    //return '<li'.($id ? ' id="'.$id.'"' : '').(!empty($classes) ? ' class="'.implode(' ', $classes).'"' : '').'>';
  }

  public function renderChildBody()
  {
    return $this->getLink() ? $this->renderLink() : $this->renderLabel();
  }

  public function renderLink()
  {
    return $this->getLink()->text($this->__($this->getLabel()))->render();
  }

  public function renderLabel()
  {
    return $this->__($this->getLabel());
  }

  protected function __($text)
  {
    return $this->getOption('translate') ? $this->i18n->__($text) : $text;
  }

  public function callRecursively()
  {
    $args = func_get_args();
    $arguments = $args;
    unset($arguments[0]);

    call_user_func_array(array($this, $args[0]), $arguments);

    foreach ($this->children as $child)
    {
      call_user_func_array(array($child, 'callRecursively'), $args);
    }

    return $this;
  }
  
  public function getPathAsString($separator = ' > ')
  {
    $children = array();
    $obj = $this;

    do
    {
      $children[] = $this->__($obj->getLabel());
    }
    while ($obj = $obj->getParent());

    return implode($separator, array_reverse($children));
  }

  public function toArray()
  {
    $array = array(
      'name'      => $this->getName(),
      'level'     => $this->getLevel(),
      'options'   => $this->getOptions(),
      'children'  => array()
    );
    
    foreach ($this->children as $key => $child)
    {
      $array['children'][$key] = $child->toArray();
    }
    
    return $array;
  }

  public function debug()
  {
    return $this->toArray();
  }

  public function fromArray($array)
  {
    $this->name($array['name']);
    
    if (isset($array['level']))
    {
      $this->level($array['level']);
    }
    if (isset($array['options']))
    {
      $this->setOptions($array['options']);
    }

    if (isset($array['children']))
    {
      foreach ($array['children'] as $name => $child)
      {
        $this->addChild($name)->fromArray($child);
      }
    }

    return $this;
  }

  public function isFirst()
  {
    return 1 === $this->num;
  }

  public function isLast()
  {
    return $this->parent && ($this->parent->count() === $this->num);
  }

  /**
   * Interfaces implementations
   */

  public function count()
  {
    return count($this->children);
  }

  public function getIterator()
  {
    return new ArrayObject($this->children);
  }

  public function current()
  {
    return current($this->children);
  }

  public function next()
  {
    return next($this->children);
  }

  public function key()
  {
    return key($this->children);
  }

  public function valid()
  {
    return false !== $this->current();
  }

  public function rewind()
  {
    return reset($this->children);
  }

  public function offsetExists($name)
  {
    return isset($this->children[$name]);
  }

  public function offsetGet($name)
  {
    return $this->getChild($name);
  }

  public function offsetSet($name, $value)
  {
    return $this->addChild($name)->setLabel($value);
  }

  public function offsetUnset($name)
  {
    return $this->removeChild($name);
  }

  /**
   * Service getters
   */
  public function getI18n()
  {
    return $this->i18n;
  }

  public function getHelper()
  {
    return $this->helper;
  }

  public function getUser()
  {
    return $this->user;
  }
}