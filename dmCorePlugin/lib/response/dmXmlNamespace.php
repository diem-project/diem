<?php

/**
 * dmXmlNamespace
 *
 * @author TheCelavi
 */
class dmXmlNamespace {
    protected 
    $namespace,
    $schema,
    $tags;
    
    public function __construct($namespace, $schema, array $tags = array()) {
        $this->namespace = $namespace;
        $this->schema = $schema;
        $this->tags = $tags;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function setNamespace($namespace) {
        $this->namespace = $namespace;
        return $this;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema($schema) {
        $this->schema = $schema;
        return $this;
    }

    public function addTag($name, array $attributes = array(), $value = null) {
        $this->tags[$name] = array(
            'name' => $name,
            'attributes' => $attributes,
            'value' => $value
        );
        return $this;
    }
    
    public function getTag($name) {
        if (isset ($this->tags[$name])) return $this->tags[$name];
        return null;
    }
    
    public function getTags() {
        return $this->tags;
    }
    
    public function setTag($name, array $attributes = array(), $value = null) {
        if (isset ($this->tags[$name])) {
            $this->tags[$name] = array(
                'name' => $name,
                'attributes' => $attributes,
                'value' => $value
                );
            return $this;
        }
        else return $this->addTag ($name, $attributes, $value);        
    }
    
    public function removeTag($name) {
        if (isset ($this->tags[$name])) unset($this->tags[$name]);
        return $this;
    }
    
    public function clearTags() {
        $this->tags = array();
        return $this;
    }
    
    public function renderNamespace() {
        return 'xmlns:' . $this->namespace . '"'. $this->schema .'"';        
    }
    
    public function renderTag($tagName) {
        if (!isset ($this->tags[$tagName])) return '';
        $attrStr = '';
        $tag = $this->tags[$tagName];
        foreach ($a = $tag['attributes'] as $key=>$value) {
            $attrStr .= sprintf('%s="%s" ', $key, $value);
        }
        if ($tag['value']) return sprintf ('<%s %s>%s</%s>', $tagName, $attrStr, $tag['value'], $tagName);
        else return  sprintf ('<%s %s/>', $tagName, $attrStr);
    }
    
    public function renderTags() {
        $tagsStr = '';
        foreach ($t = $this->tags as $key=>$value) $tagsStr .= $this->renderTag ($key) . '\n';
        return $tagsStr;
    }
}

?>
