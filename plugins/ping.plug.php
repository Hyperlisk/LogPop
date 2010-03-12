<?php

$plugins = array(
  array('message','notice','func',function($message){
    if(!$message->isCTCP || $message->CTCP != 'PING'){
      return;
    }
    $user = $message->username;
    $pings = $message->logpop->plugVarGet('_PING_PLUG',$user);
    if($pings === null){
      return;
    }
    $channel = $pings[0];
    $time = microtime(1) - ((float)$pings[1]);
    $message->logpop->sendToChannel($channel,"$user: PING took $time seconds.");
  }),

  array('trigger','ping','func',function($args,$message){
    $channel = $message->channel;
    $user = preg_replace('/^me$/',$message->username,$message->data);
    if(preg_match('/^\s*#/',$user)){
      $message->logpop->sendToChannel($channel,"I dun wanna ping a channel...");
      return;
    }
    $pings = $message->logpop->plugVarSet('_PING_PLUG',$user,array($channel,(string)microtime(1)));
    echo "Sending ping to $user\n";
    $message->logpop->sendCTCP($user,'PING',$message->logpop->getUsername());
  })
);

?>