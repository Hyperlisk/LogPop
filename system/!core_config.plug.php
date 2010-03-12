<?php
  
/*
\/  Load the config file...
/\
*/
$lp->funcs['loadConfig'] = function() use (&$lp){
  $config = array();
  $section = '';
  foreach(array_map('trim',file(CONFIG_FILE)) as $line){
    if(strpos($line,';') !== false){
      $line = substr($line,0,strpos($line,';'));
    }
    if(empty($line)){
      continue;
    }
    if($line[0] == '[' && $line[strlen($line)-1] == ']'){
      $section = strtolower(substr($line,1,strlen($line)-2));
      if(!isset($config[$section])){
        $config[$section] = array();
        $config[$section][0] = 1;
      } else {
        ++$config[$section][0];
      }
      $config[$section][$config[$section][0]] = array();
    } else {
      list($key,$val) = array_map('trim',explode('=',$line));
      $config[$section][$config[$section][0]][$key] = preg_match('/^\d+$/',$val)?(int)$val:$val;
    }
  }
  $lp->config = $config;
  return $config;
};
  
$lp->funcs['getConfigValue'] = function($section,$key) use (&$lp){
  $section = strtolower($section);
  if(!isset($lp->config[$section]) || !isset($lp->config[$section][$key])){
    return false;
  }
  return $lp->config[$section][$key];
};

$lp->funcs['setConfigValue'] = function($section,$key,$value) use (&$lp){
  $section = strtolower($section);
  if(!isset($lp->config[$section])){
    $lp->config[$section] = array();
  }
  $lp->config[$section][$key] = $val;
  $buf = array();
  foreach($lp->config as $name=>$section){
    $buf[] = "[$name]";
    foreach($section as $k=>$v){
      $buf[] = "$k = $v";
    }
  }
  file_put_contents(CONFIG_FILE,implode("\r\n",$buf));
  return $val;
};

?>