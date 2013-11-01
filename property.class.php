<?php
require_once('CIDR.php');
/**
 * property.class.php
 *
 * @author Forrest Vodden
 * @created 9/12/13
 */
 
class Property {
  private $__identity = null;
  private $__attributes = array();
  private $__defaults = array();
  
  public function __construct($identity, $attributes = array(), $defaults = array()){
    $this->__identity = $identity;
    $this->__defaults = $defaults;
    $this->__attributes = self::__parseAttributes($attributes);
  }
  
  private static function __parseAttributes($attrs){
    $arrObj = is_object($attrs) ? get_object_vars($attrs) : $attrs;
    foreach ($arrObj as $key => $val) {
      $val = (is_array($val) || is_object($val)) ? self::__parseAttributes($val) : $val;
      $arr[$key] = $val;
    }

    return $arr;
  }

/**
 * checkIp - ensures that request came from actual gateway
 *
 */
  public function checkIp(){
    if(NULL !== ($ip = $this->get('gateway_ip'))){
      if(is_string($ip)){
        $pieces = explode('/', $ip);
        $cidr = null;

        $singleIp = $ip;
        if(count($pieces) == 2){
          $cidr = $pieces[1];
          $singleIp = $pieces[0];
        }

        $userIp = $_SERVER['REMOTE_ADDR'];

        if($userIp == '127.0.0.1') return true;
        if(is_null($cidr)){
          if($userIp == $singleIp){
            return true;
          }

          return false;
        }

        if(CIDR::IPisWithinCIDR($userIp, $ip)){
          return true;
        }
      }

      return false;
    }

    return true;
  }
  
  public function getKeys(){
    return array_keys($this->__attributes);
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