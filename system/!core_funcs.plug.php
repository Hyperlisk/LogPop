<?php

/*
\/  Stubs for messages...
/\
*/

$lp->funcs['connect'] = function($arg) use (&$lp){
  $args = func_get_args();
  $server = new Server($lp);
  if(!call_user_func_array(array($server,'connect'),$args)){
    return false;
  }
  $lp->servers[] = $server;
  return $server;
};
  
$lp->funcs['send'] = function($data) use (&$lp){
  return $lp->current_server->send($data);
};

$lp->funcs['sendToChannel'] = function($channel,$data) use (&$lp){
  return $lp->current_server->sendMessage($channel,$data);
};

$lp->funcs['sendToUser'] = function($user,$data) use (&$lp){
  return $lp->current_server->sendMessage($user,$data);
};

$lp->funcs['sendCTCP'] = function($where,$ctcp,$data='') use (&$lp){
  return $lp->current_server->sendMessage($where,chr(1).$ctcp.(!empty($data)?" $data":'').chr(1));
};

$lp->funcs['sendNotice'] = function($where,$data) use (&$lp){
  return $lp->current_server->sendNotice($where,$data);
};

$lp->funcs['sendCTCPReply'] = function($where,$ctcp,$data='') use (&$lp){
  return $lp->current_server->sendNotice($where,chr(1).$ctcp.(!empty($data)?" $data":'').chr(1));
};

$lp->funcs['setNick'] = function($nick) use (&$lp){
  return $lp->current_server->setNick($nick);
};

$lp->funcs['join'] = function($channel) use (&$lp){
  return $lp->current_server->join($channel);
};

$lp->funcs['part'] = function($channel) use (&$lp){
  return $lp->current_server->part($channel);
};

$lp->funcs['quit'] = function($message) use (&$lp){
  return $lp->current_server->quit($message);
};

?>