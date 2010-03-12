<?php

$plugins = array(
  // Triggers to handle and how to handle them
  array('trigger', 'connect', 'func', function($args,$message){
    $message->logpop->sendToChannel($message->channel,"Connecting to {$args[0]}...");
    $message->logpop->connect($args[0]);
  }, 999)
);

?>