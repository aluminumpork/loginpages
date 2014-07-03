<?php
require_once('class.loggly.php');
require_once('config.php');
require_once('common.php');
require_once('navigator.class.php');
require_once('rain.tpl.class.php');

if(Navigator::init()){
  if(!Navigator::doWeBelongHere()){
    Navigator::redirectTo();
  }
  
  Navigator::render();
}
