<?php

$f = function($message){
  $okay = array('PING','VERSION','TIME');
  if(!$message->isCTCP || !in_array($message->CTCP,$okay)){
    return;
  }
  $user = $message->username;
  echo $message->CTCP." CTCP Reply sent to $user\n";
  $bot =& $message->logpop;
  switch($message->CTCP){
    case 'PING':
      $bot->sendCTCPReply($user,'PING',$message->data);
      break;

    case 'VERSION':
      $bot->sendCTCPReply($user,'VERSION',$message->logpop->config['core'][1]['version_string']);
      break;

    case 'TIME':
      $bot->sendCTCPReply($user,'TIME','Today');
      break;
  }
};

$plugins = array(
  // Messages to handle and how to handle them
  array('message', 'privmsg', 'func', $f),
  //array('message', 'notice', 'func', $f),
  array('message', 'ping', 'func', function($message){
    $p = "PONG {$message->data}\n";
    echo $p;
    $message->logpop->send($p);
  })
);

?>