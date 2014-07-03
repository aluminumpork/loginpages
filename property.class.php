<?php
require_once('standard_object_base.class.php');
require_once('CIDR.php');
/**
 * property.class.php
 *
 * @author Forrest Vodden
 * @created 9/12/13
 */
 
class Property extends StandardObjectBase {
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
}