<?php

$plugins = array(
  // Triggers to handle and how to handle them

  // Reload plugins, display a message...
  array('trigger', 'reload', 'func', function($args,$message){
    $message->logpop->loadConfig();
    $err = $message->logpop->loadPlugins();
    if(empty($err)){
      $message->logpop->sendToChannel($message->channel,"Reloaded...");
    } else {
      $message->logpop->sendToChannel($message->channel,"Some plugins failed to update. Check $err for details.");
    }
  }, 999),

  // Reload plugins, don't display a message...
  array('trigger', 'sreload', 'func', function($args,$message){
    $message->logpop->loadPlugins();
  }, 999),

  // Join a channel...
  array('trigger', 'join', 'func', function($args,$message){
    $message->logpop->join($args[0]);
  }, 999),

  // Part a channel...
  array('trigger', 'part', 'func', function($args,$message){
    $message->logpop->part($args[0]);
  }, 999),

  // Quit the server...
  array('trigger', 'quit', 'func', function($args,$message){
    $message->logpop->quit(implode(' ',$args));
  }, 999)
);

?>