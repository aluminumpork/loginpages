<?php
require_once('config.php');
require_once('common.php');
require_once('rain.tpl.class.php');
require_once('properties.class.php');

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

if(($p = checkArr($req, $_GET)) !== FALSE){
  raintpl::configure('tpl_dir', "templates/");
  raintpl::configure('tpl_ext', "tpl.inc");
  raintpl::configure('path_replace', false);

  // look up property information in properties.json
  $properties = new Properties(file_get_contents('properties.json'));
  $property = $properties->get($p['identity']);

  if(!$property->checkIp()){
    $p['page'] = 'error';
    $p['error'] = 'Unauthorized gateway';
  }

  // retrieve all configuration values from the property
  $parameters = $property->getAll();

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
  $tpl->draw($p['page']);
} else {
  throw new Exception('Required variables are missing!');
}
