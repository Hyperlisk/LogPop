<?php

$lp->funcs['verifyIdentity'] = function($trigger,$message) use (&$lp){
  $key = md5($message->username.$lp->getUsername());
  $lp->plugVarSet('_CORE_IDENTITY',$key,array($trigger,$message));
  $lp->send('WHOIS '.$message->username);
};

$plugins = array(
  array('message',RPL_WHOISUSER,'func',function($message){
    echo $message->data."\n";
  }),
  
  array('message','330','func',function($message){
    $key = md5($message->middle[1].$message->logpop->getUsername());
    if(strpos($message->data,'is logged in as') === false){
      return;
    }
    $message->logpop->plugVarSet('_CORE_IDENTITY',$message->middle[1],true);
    list($trigger,$msg) = $message->logpop->plugVarGet('_CORE_IDENTITY',$key);
    $message->logpop->runIt('trigger','system_triggers',$trigger,$msg);
    $message->logpop->runIt('trigger','general_triggers',$trigger,$msg);
  }),
  
  array('message','part','func',function($message){
    $message->logpop->plugVarSet('_CORE_IDENTITY',$message->username,false);
  }),
  
  array('message','nick','func',function($message){
    $message->logpop->plugVarSet('_CORE_IDENTITY',$message->username,false);
    $message->logpop->plugVarSet('_CORE_IDENTITY',$message->data,false);
  }),
  
  array('message','quit','func',function($message){
    $message->logpop->plugVarSet('_CORE_IDENTITY',$message->username,false);
  }),

  array('trigger','test','func',function($args,$message){
    $message->logpop->verifyIdentity('say',$message);
  })
);

?>