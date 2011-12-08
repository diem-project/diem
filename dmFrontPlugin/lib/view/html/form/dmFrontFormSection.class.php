<?php

class dmFrontFormSection implements Iterator {
    
    protected $sectionLabel, $fields, $sectionHelp;
    
    private $position;
    
    public function __construct(array $fields, $sectionLabel, $sectionHelp = null, $configured = false) {
        $this->position = 0;        
        $this->sectionLabel = $sectionLabel;
        $this->sectionHelp = $sectionHelp;        
        if (!$configured) $this->configure($fields);
        else $this->fields = $fields;
           
    }
    
    protected function configure(array $fields) {
        $this->fields = array();
        foreach ($fields as $field) {
            $this->fields[] = $this->configureField($field);
        }
    }

    protected function configureField($field) {
        // TYPE : form_field, separator, subsection
        // TODO : type: nested_form???
        if (is_array($field)) {
            return array(
                'name'          =>          $field['name'],
                'type'          =>          (isset ($field['type'])) ? $field['type'] : 'form_field',
                'is_big'        =>          (isset($field['is_big'])) ? $field['is_big'] : ($field['type'] == 'separator' || $field['type'] == 'subsection' || $field['type'] == 'nested_form') ? true : false,
                'options'       =>          (isset ($field['options'])) ? $field['options'] : null,
                'bound_to'      =>          array()
            );
        } else {
            return array(
                'name'          =>          $field,
                'type'          =>          'form_field',
                'is_big'        =>          false,
                'options'       =>          null,
                'bound_to'      =>          array()
            );
        }
    }

    public function getSectionLabel() {
        return $this->sectionLabel;
    }

    public function setSectionLabel($sectionLabel) {
        if ($sectionLabel == "") return $this; // TODO throw error?
        $this->sectionLabel = $sectionLabel;
        return $this;
    }

    public function getFields() {
        return $this->fields;
    }

    public function setFields(array $fields) {
        $this->fields = $fields;
        return $this;
    }    
    
    public function setSectionHelp($sectionHelp) {
        $this->sectionHelp = (trim($sectionHelp)!='') ? $sectionHelp : null;
    }

    public function getSectionHelp() {
        return $this->sectionHelp;
    }

    public function current() {
        return $this->fields[$this->position];
    }
    public function key() {
        return $this->position;
    }
    
    public function next() {
        ++$this->position;
    }
    public function rewind() {
        $this->position = 0;
    }
    public function valid() {
        return isset($this->fields[$this->position]);
    }
    
    public function countFormFields() {
        $count = 0;
        foreach ($this->fields as $field) {
            if ($field['type'] == 'form_field') $count++;
        }
        return $count;
    }
    
    public function isWithoutFields() {
        return $this->countFormFields();
    }
    
    public function removeNonExisting($form) {
        $remove = array();
        $compiledFields = $this->fields;
        foreach ($this->fields as $key => $field) {
            if ($field['type'] == 'form_field' && !isset($form[$field['name']])) $remove[] = $key;
        }
        $remove = array_reverse($remove);
        foreach ($remove as $index) unset($compiledFields[$index]);
        if (!$this->isWithoutFields()) $compiledFields = $this->removeBoundFields($compiledFields);
        return new dmFrontFormSection($compiledFields, $this->getSectionLabel(), $this->getSectionHelp(), true);
        
    }
    
    
    protected function removeBoundFields($fields) {
        $remove = array();
        foreach ($fields as $key=>$field) {
            if ($field['type'] != 'form_field') {
                $signal = true;
                foreach ($field['bound_to'] as $bound) {
                    if ($this->fieldExist($bound, $fields)) {
                        $signal = false;
                        break;
                    }                    
                }
                if ($signal) $remove[] = $key;
            }
        }
        $remove = array_reverse($remove);
        foreach ($remove as $index) {
            unset ($fields[$index]);
        }
        return $fields;
    }
    
    private function fieldExist($fieldName, $fields) {
        foreach ($fields as $field) if ($field['name'] == $fieldName) return true;
        return false;
    }
}
