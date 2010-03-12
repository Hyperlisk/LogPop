<?php

/*
\/  Register the plugin triggers and messages...
/\
*/
$lp->funcs['register'] = function($arr) use (&$lp){
  if(!is_array($arr)){
    return false;
  }
  if(!is_array($arr[0])){
    $arr = array($arr);
  }
  foreach($arr as $plugin){
    $spot = false;
    switch(array_shift($plugin)){
      case 'trigger':
        if($lp->registering_system_plugin){
          $spot = 'system_triggers';
        } else {
          $spot = 'general_triggers';
        }
        break;
      case 'message':
        if($lp->registering_system_plugin){
          $spot = 'system_messages';
        } else {
          $spot = 'general_messages';
        }
        break;
      default:
        echo 'Unknown plugin type...';
        break;
    }
    $lp->registerIt($plugin,$spot);
  }
};

$lp->funcs['registerIt'] = function($plugin,$where) use (&$lp){
  $what = array_shift($plugin);
  if(!isset($where[$what])){
    $lp->{$where}[$what] = array();
  }
  $lp->{$where}[$what][] = $plugin;
};

?>