<?php

$plugins = array(
  // Messages to handle and how to handle them
  array('message', 'privmsg', 'func', function($message){
    // Call a trigger with ".trigger"
    if(strpos($message->data,$message->logpop->getTrigger()) === 0){
      $trigger = substr($message->params[0],strlen($message->logpop->getTrigger()));
      $message->data = explode(' ',$message->data);
      array_shift($message->data);
      $message->data = implode(' ',$message->data);
      $message->logpop->runTrigger($trigger,$message);
    }

    // Call a trigger with "log[pop]: trigger"
    if(strpos($message->data,$message->logpop->getUsername()) === 0){
      $message->data = explode(' ',$message->data);
      array_shift($message->data);
      array_shift($message->args);
      array_shift($message->params);
      $trigger = array_shift($message->data);
      $message->data = implode(' ',$message->data);
      $message->logpop->runTrigger($trigger,$message);
    }

  })
);

?>