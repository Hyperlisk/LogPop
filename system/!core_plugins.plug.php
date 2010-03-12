<?php

/*
\/  Plugin variables...
/\
*/
$lp->funcs['plugVarSet'] = function($ns,$key,$val) use (&$lp){
  if(!isset($lp->plug[$ns])){
    $lp->plug[$ns] = array();
  }
  $lp->plug[$ns][$key] = $val;
};

$lp->funcs['plugVarGet'] = function($ns,$key) use (&$lp){
  if(!isset($lp->plug[$ns])){
    $lp->plug[$ns];
  }
  if(!isset($lp->plug[$ns][$key])){
    $lp->plug[$ns][$key] = array();
  }
  return $lp->plug[$ns][$key];
};

/*
\/  Load system and general plugins...
/\
*/
$lp->funcs['loadPlugins'] = function() use (&$lp){
  // In case we fail to load a plugin...
  $_failed = array();

  // Clear old plugins, set system flag...
  $lp->system_triggers = array();
  $lp->system_messages = array();
  $lp->general_triggers = array();
  $lp->general_messages = array();
  $lp->registering_system_plugin = 1;

  // Register plugins
  $_plugins = glob(SYSTEM_DIR.'*.plug.php');
  echo "Loading system plugins...\n";
  for($_i=0;$_i<2;$_i++){
    foreach($_plugins as $_plugin){
      $_file = str_replace(array(SYSTEM_DIR,PLUGIN_DIR),'_',$_plugin);
      echo "Loading '".str_replace(array(SYSTEM_DIR,PLUGIN_DIR),'',$_plugin)."'...";
      if(!file_exists(CACHE_DIR.$_file) || filemtime(CACHE_DIR.$_file) < filemtime($_plugin)){
        $_code = PHP_PATH.' -l '.$_plugin;
        $_check = `$_code`;
        if(substr($_check,0,16) != 'No syntax errors'){
          $_failed[] = $_plugin.' has a syntax error: '.str_replace(array("\r\n","\n"),array("\n","\r\n"),$_check)."\r\n";
          continue;
        }
        touch(CACHE_DIR.$_file);
      }
      $plugins = array();
      include $_plugin;
      if(!empty($plugins)){
        $lp->register($plugins);
      }
      echo " Done.\n";
    }
    if(!$lp->registering_system_plugin){
      break;
    }
    $lp->registering_system_plugin = 0;
    $_plugins = glob(PLUGIN_DIR.'*.plug.php');
    echo "Loading user plugins...\n";
  }
  if(!empty($_failed)){
    echo "Some plugins failed to load, here's the details:\n".implode('',$_failed)."\n";
    // Open a new log...
    $_n = 'plugin_log_'.time().'.txt';
    $_f = fopen($_n,'w');
    fwrite($_f,implode('',$_failed));
    fclose($_f);
    return $_n;
  }
  return '';
}

?>