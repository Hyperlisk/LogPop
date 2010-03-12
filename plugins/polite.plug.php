<?php

$plugins = array(
  // Messages to handle and how to handle them
  array('message', 'privmsg', 'func', function($message){
    if(preg_match('/(hi|hello|hey|hola).*?'.preg_quote($message->logpop->getUsername()).'/i',$message->data)){
      $message->logpop->sendToChannel($message->channel,'Hola, '.$message->username.'!');
    }
  })
);

?>