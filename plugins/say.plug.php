<?php

$plugins = array(
  // Triggers to handle and how to handle them
  array('trigger', 'say', 'func', function($args,$message){
    $message->logpop->sendToChannel($message->channel,$message->data);
  })
);

?>