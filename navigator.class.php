<?php
require_once('config.php');
require_once('common.php');
require_once('navigator.class.php');
require_once('rain.tpl.class.php');

require_once('page_order.class.php');
require_once('properties.class.php');

define('NAV_IS_LAST_PAGE', 8);
define('NAV_IS_FIRST_PAGE', 16);
define('NAV_ERROR', 32);

class Navigator {
  private $__currentPage = null;
  private $__propertyIdentity = null;
  private $__propertyObj = null;
  private $__params = array();
  
  private function __construct(){}
  private static function &__getInstance(){
    static $instance = array();
    if(!$instance){
      $obj = new self();
      $instance[0] =& $obj;
    }
    
    return $instance[0];
  }
  
  public static function init(){
    $_this =& self::__getInstance();

    if(!$_this->__initParams()){ return false; }
    if(!$_this->__initRainTpl()){ return false; }
    if(!$_this->__initProperty()){ return false; }
    return $_this->__initPageOrder();
  }
  
  private function __initParams(){
    // variables the mikrotik must send
    $req = array(
      'page',
      'identity',
      'mac',
      'link-login',
      'link-orig'
    );
    
    $p = $_GET;
    if(!isset($p['page'])) $p['page'] = null;
    if(($this->__params = checkArr($req, $p)) !== FALSE){
      $this->__propertyIdentity = strtolower($this->__params['identity']);
      return true;
    }
    
    self::fail('The Mikrotik did not send all of the expected parameters!');
  }
  
  private function __initRainTpl(){
    raintpl::configure('tpl_dir', "templates/");
    raintpl::configure('tpl_ext', "tpl.inc");
    raintpl::configure('path_replace', false);
    raintpl::configure('php_enabled', true);
    
    return true;
  }
  
  private function __initProperty(){
    // look up property information in properties.json
    $properties = new Properties(file_get_contents('properties.json'));
    $this->__propertyObj = $properties->get($this->__propertyIdentity);
    
    if(is_object($this->__propertyObj)){
      return true;
    }
    
    self::fail(sprintf('The property identity provided (%s) does not map to an actual property', $this->__propertyIdentity));
  }
  
  private function __initPageOrder(){
    $this->__pageOrder = new PageOrder(file_get_contents('page_order.json'), $this->__propertyObj);
    if(!isset($this->__params['page'])) $this->__params['page'] = self::getInitialPage();
    $pageName = $this->__params['page'];
    
    if($pageObj = $this->__pageOrder->get($pageName)){
      $this->__sequencedPages = $this->__pageOrder->getSequenced();
      $this->__currentPageObj =& $pageObj;
      $this->__currentPage = $this->__currentPageObj->getIdentity();
      
      return true;
    }
    
    // the page is invalid, redirect to the start page
    self::redirectTo();
  }
  
  private function __getSequencedPage($type){
    $pages = $this->__sequencedPages;
    $curPage = $this->__currentPage;

    if(FALSE !== ($idx = array_search($curPage, $pages))){
      if($type == 'next' && ($idx + 1) == count($pages)) return NAV_IS_LAST_PAGE;
      if($type == 'prev' && $idx == 0) return NAV_IS_FIRST_PAGE;
      
      return ($type == 'next') ? $pages[$idx + 1] : $pages[$idx - 1];
    }
    
    return NAV_ERROR;
  }
  
  public static function getInitialPage(){
    $_this =& self::__getInstance();
    $pages = $_this->__pageOrder->getSequenced(false);

    foreach($pages as $p){
      if($p->get('initial_page') == true){
        return $p->getIdentity();
      }
    }
    
    return NAV_ERROR;
  }
  
  public static function doWeBelongHere(){
    $_this =& self::__getInstance();
    $pages = $_this->__pageOrder->getSequenced(true, true);
    $curPage = $_this->__currentPage;
    
    if(array_search($curPage, $pages) === FALSE){
      return false;
    }
    
    return true;
  }
  
  public static function getNextPage(){
    $_this =& self::__getInstance();
    return $_this->__getSequencedPage('next');
  }
  
  public static function getPrevPage(){
    $_this =& self::__getInstance();
    return $_this->__getSequencedPage('prev');
  }
  
  public static function redirectTo($page = null){
    $_this =& self::__getInstance();
    $p = $_this->__params;
    
    if($page == null){
      $p['page'] = $_this->getInitialPage();
    }
    
    $formattedParams = array();
    foreach($p as $pKey => $pVal){
      $formattedParams[] = "$pKey=$pVal";
    }
    
    $url = BASE_URL . '?' . implode('&', $formattedParams);
    header('Location: ' . $url);
    exit;
  }
  
  public static function render(){
    $_this =& self::__getInstance();
    
    // setup default values
    $parameters = $_this->__propertyObj->getAll();
    $parameters['asset_path'] = TEMPLATE_ASSET_ROOT;
    $jsonParams = json_encode(array_merge(array(
      'next_page' => self::getNextPage(),
      'prev_page' => self::getPrevPage()
      ),
      $_this->__params
    ));
    $parameters = array_merge($parameters, $_this->__params);
    
    if(isset($parameters['next_page'])) unset($parameters['next_page']);
    if(isset($parameters['prev_page'])) unset($parameters['prev_page']);
  
    // remove dashes from variable names
    $parameters = modifyKeys('/([\w](?=-))-/s', '$1_', $parameters);
    $parameters['json_redirect_params'] = $jsonParams;
  
    $parameters = array_map('urldecode', $parameters);
    if(isset($_GET['error']) && !empty($_GET['error'])) $parameters['error'] = $_GET['error'];

    header('Cache-control: no-cache');

    // initialize raintpl engine
    $tpl = new rainTPL();
    $tpl->assign($parameters);
  
    $tpl->draw($_this->__params['page']);
  }
  
  private static function fail($msg){
    prd($msg);
    die();
  }
}