<?php
require_once('property.class.php');

/**
 * properties.class.php
 *
 * @author Forrest Vodden
 * @created 9/12/13
 */
 
class Properties {
  private $__json = null;
  private $__data = null;
  
  private $__children = array();

  public function __construct($json){
    $this->__json = $json;
    $this->__load();
  }
  
  private function __load(){
    if(!is_null($this->__json)){
      $this->__data = json_decode($this->__json);

      if(isset($this->__data->{"__defaults__"})){
        $defaults = $this->__data->__defaults__;
        unset($this->__data->__defaults);
      }

      foreach($this->__data as $key => $value){
        $key = strtolower($key);
        $this->__children[$key] = new Property($key, $value, $defaults);
      }
    }
  }
  
  public function get($identity){
    $identity = strtolower($identity);
    
    if(isset($this->__children[$identity])){
      return $this->__children[$identity];
    }
    
    return null;
  }
}