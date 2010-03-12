<?php

class LogPop {

  // Config data...
  public $config = array();

  // Plugin data...
  public $plug = array();

  // Default username.
  public $default_username = false;

  // Default trigger.
  public $trigger = '.';

  // Array of the servers it's connected to.
  public $servers = array();

  // Users with access.
  public $access = array();

  // System plugins...
  public $registering_system_plugin = true;
  public $system_triggers = array();
  public $system_messages = array();

  // General plugins...
  public $general_triggers = array();
  public $general_messages = array();
  
  // Functions
  public $funcs = array();

  // Current server it is getting a message from
  public $current_server = false;
  
  private static $me = false;

  /*
  \/  Set username and load plugins...
  /\  
  */
  private function __construct(){
    $this->default_username = 'log[pop]';
    $lp =& $this;
    foreach(glob(SYSTEM_DIR.'!*') as $file){
      require $file;
      echo "Loading functions from [".basename($file)."]\n";
    }
    $this->loadConfig();
  }
  
  public static function &inst(){
    if(self::$me === false){
      self::$me = new LogPop();
    }
    return self::$me;
  }
  
  /*
  \/  Call our loaded functions
  /\
  */
  public function __call($name,$args){
    $r = new ReflectionClass('LogPop');
    if($r->hasMethod($name)){
      return call_user_func_array(array($this,$name),$args);
    }
    if(!isset($this->funcs[$name]) || !is_callable($this->funcs[$name])){
      return false;
    }
    return call_user_func_array($this->funcs[$name],$args);
  }

}

?>