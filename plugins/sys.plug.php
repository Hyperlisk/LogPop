<?php

$plugins = array(
  array('trigger','uptime','func',function($args,$message){
    $message->logpop->sendToChannel($message->channel,`uptime`);
  })
);

?>
