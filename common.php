<?php
/**
 * common.php
 *
 * @author Forrest Vodden
 * @created 9/12/13
 */
 
function pr($v){
  echo '<pre>';
  print_r($v);
  echo '</pre>';
}

function prd($v){
  pr($v);
  die();
}

function modifyKeys($regxp, $replace, $target = array()){
  foreach($target as $key => $val){
    if(null !== ($result = preg_replace($regxp, $replace, $key))){
      unset($target[$key]);
      $target[$result] = $val;
    }
  }

  return $target;
}

function checkArr($keys, $arr){
  if(!is_array($keys)) $keys = array($keys);
  
  $found = array();
  foreach($keys as $k){
    if(array_key_exists($k, $arr)){
      $found[$k] = $arr[$k];
      continue;
    }
    
    return false;
  }
  
  return $found;
}

function getRemoteIP(){
  return $_SERVER['REMOTE_ADDR'];
}

function generateSessionID(){
  $now = time();
  $str = md5(json_encode($_GET)) . $now;

  return $str;
}