<?php

$plugins = array(
  // Triggers to handle and how to handle them
  array('trigger', 'flip', 'func', function($args,$message){
    $message->logpop->sendToChannel($message->channel,rand(1,10)%2?'Heads':'Tails');
  }),
  array('trigger','source','text','http://github.com/Hyperlisk/LogPop')
);

?>
