<?php

error_reporting(E_ALL|E_STRICT);
require_once "./core/Core.php";

$lp = LogPop::inst();
if(!isset($lp->config['core'])){
  die("Error in config file '".CONFIG_FILE."'. Missing required [core] section.");
}

define('PHP_PATH',$lp->config['core'][1]['PHP']);
date_default_timezone_set('America/Los_Angeles');
$lp->loadPlugins();
$lp->loadAccess();
$lp->runMessage(new Message(":log[pop] onstart log[pop] :Started\n",$lp));

$real_default = $lp->config['core'][1]['username'];
if(!isset($lp->config['server'])){
  die("No servers defined, quitting~");
}
foreach($lp->config['server'] as $server_num=>$server_config){
  if(!is_array($server_config)){
    continue;
  }
  if(!isset($server_config['host'])){
    echo "No host name specified for server $server_num, continuing...";
    continue;
  }
  $host = $server_config['host'];
  $lp->setDefaultUsername(isset($server_config['username'])?$server_config['username']:$real_default);
  $s = $lp->connect($server_config['host'],isset($server_config['port'])?$server_config['port']:6667,isset($server_config['pass'])?$server_config['pass']:'','');
  if($s !== false){
    $s->join(str_replace(' ','',$server_config['channels']));
  }
}

$lp->setDefaultUsername($real_default);
  
if(isset($s)){
  $lp->run();
}

?>