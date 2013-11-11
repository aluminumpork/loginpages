<?php
require_once('class.loggly.php');
require_once('config.php');
require_once('common.php');
require_once('rain.tpl.class.php');
require_once('properties.class.php');

use loggly\Loggly as Loggly;

Loggly::setToken('d92fe5b9-a039-491c-9314-37c17a2fd760');

$sessId = generateSessionID();
$identity = null;
$tpl = null;

// required variables sent from mikrotik
$req = array(
  'page',
  'identity',
  'mac',
  'link-login',
  'link-orig'
);

Loggly::write(array(
  'msg' => 'Page request',
  'gateway_ip' => getRemoteIP(),
  'sessid' => $sessId,
  'params' => $_GET
), array('loginpage'));

if(($p = checkArr($req, $_GET)) !== FALSE){
  raintpl::configure('tpl_dir', "templates/");
  raintpl::configure('tpl_ext', "tpl.inc");
  raintpl::configure('path_replace', false);

  // look up property information in properties.json
  $properties = new Properties(file_get_contents('properties.json'));
  $property = $properties->get($p['identity']);

  if($property == null){
    Loggly::write(array(
      'msg' => 'Unable to identify property',
      'gateway_ip' => getRemoteIP(),
      'identity' => $p['identity'],
      'action' => 'redirect_to_error',
      'sessid' => $sessId
    ), array('loginpage'));

    $p['page'] = 'error';
    $p['error'] = 'Unable to identify property';
  }

  $parameters = array();

  if($property){
    if(!$property->checkIp()){
      Loggly::write(array(
        'msg' => 'Gateway IP not allowed',
        'gateway_ip' => getRemoteIP(),
        'action' => 'redirect_to_error',
        'sessid' => $sessId
      ), array('loginpage'));

      $p['page'] = 'error';
      $p['error'] = 'Unauthorized gateway';
    }

    // retrieve all configuration values from the property
    $parameters = $property->getAll();
  }

  // setup default values
  $parameters['asset_path'] = TEMPLATE_ASSET_ROOT;
  $jsonParams = json_encode($p);

  $parameters = array_merge($parameters, $p);

  // remove dashes from variable names
  $parameters = modifyKeys('/([\w](?=-))-/s', '$1_', $parameters);
  $parameters['json_redirect_params'] = json_encode($p);

  $parameters = array_map('urldecode', $parameters);
  if(isset($_GET['error']) && !empty($_GET['error'])) $parameters['error'] = $_GET['error'];

  header('Cache-control: no-cache');

  // initialize raintpl engine
  $tpl = new rainTPL();
  $tpl->assign($parameters);

  Loggly::write(array(
    'msg' => 'Render page',
    'gateway_ip' => getRemoteIP(),
    'params' => $parameters,
    'sessid' => $sessId
  ),array('loginpage'));

  $tpl->draw($p['page']);
} else {
  Loggly::write(array(
    'msg' => 'Required variables missing',
    'gateway_ip' => getRemoteIP(),
    'params' => $_GET,
    'action' => 'exception',
    'sessid' => $sessId
  ),array('loginpage'));

  throw new Exception('Required variables are missing!');
}
