<?php
class StandardObjectBase {
  protected $__identity = null;
  protected $__attributes = array();
  protected $__defaults = array();

  public function __construct($identity, $attributes = array(), $defaults = array()){
    $this->__identity = $identity;
    $this->__defaults = $defaults;
    $this->__attributes = self::__parseAttributes($attributes);
  }
  
  protected static function __parseAttributes($attrs){
    $arrObj = is_object($attrs) ? get_object_vars($attrs) : $attrs;
    foreach ($arrObj as $key => $val) {
      $val = (is_array($val) || is_object($val)) ? self::__parseAttributes($val) : $val;
      $arr[$key] = $val;
    }

    return $arr;
  }
  
  public function getKeys(){
    return array_keys($this->__attributes);
  }
  
  public function getIdentity(){
    return $this->__identity;
  }
  
  public function getAll(){
    $keys = $this->getKeys();
    $result = array();
    foreach($keys as $k){
      $result[$k] = $this->get($k);
    }

    foreach($this->__defaults as $defKey => $defVal){
      if(is_null($this->get($defKey))){
        $result[$defKey] = $defVal;
      }
    }

    return $result;
  }
  
  public function get($key){
    if(array_key_exists($key, $this->__attributes)){
      return $this->__attributes[$key];
    }
    
    return null;
  }
  
  public function set($key, $val){
    $this->__attributes[$key] = $val;
  }
}