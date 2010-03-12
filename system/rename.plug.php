<?php

$plugins = array(
  // Messages to handle and how to handle them
  array('message', ERR_NICKNAMEINUSE, 'func', function($message){
    $old_nick  = $message->channel;
    if(preg_match('/^(.*?)(\d+)$/',$old_nick,$nick)){
      $new_nick = $nick[1].(((int)$nick[2])+1);
    } else {
      $new_nick = $old_nick.'2';
    }
    $message->logpop->setNick($new_nick);
  })
);

?>