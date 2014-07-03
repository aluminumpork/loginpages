<?php
require_once('page.class.php');

/**
 * page_order.class.php
 *
 * @author Forrest Vodden
 * @created 7/1/14
 */
 
class PageOrder {
  private $__json = null;
  private $__data = null;
  private $__property = null;
  
  private $__children = array();

  public function __construct($json, $property){
    $this->__json = $json;
    $this->__property =& $property;
    $this->__load();
    
    $this->__buildSequence();
  }
  
  private function __load(){
    if(!is_null($this->__json)){
      $this->__data = json_decode($this->__json);
      if(isset($this->__data->pages) && is_object($this->__data->pages)){
        foreach($this->__data->pages as $key => $value){
          $key = strtolower($key);
          $this->__children[$key] = new Page($key, $value);
        }
      }
    }
  }
  
  private function __buildSequence($all = false){
    $pages = array();
    $lastOrder = 0;
    foreach($this->__children as $childKey => $childObj){
      $condKey = $childObj->get('condition_key');
      $condVal = $childObj->get('condition_value');
      
      if(is_string($condKey) && !is_null($condVal)){
        $testVal = $this->__property->get($condKey);
        if($testVal != $condVal){
          continue;
        }
      }
      
      $order = $childObj->get('order');
      if($order !== false){ $pages[$order] = $childObj; continue; }
      if($all == true) $pages[] = $childObj;
    }
    
    ksort($pages);
    return array_combine(array_keys(array_fill(0, count($pages), '')), $pages);
  }
  
  public function getSequenced($flat = true, $all = false){
    $sequenced = $this->__buildSequence($all);
    
    $flattened = array();
    if(!$flat) return $sequenced;

    foreach($sequenced as $s){
      $flattened[] = $s->getIdentity();
    }
    
    return $flattened;
  }
  
  public function get($identity){
    $identity = strtolower($identity);
    if(isset($this->__children[$identity])){
      return $this->__children[$identity];
    }
    
    return null;
  }
}