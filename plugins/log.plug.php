<?php

if(!defined('LOG_FILE')){
  define('LOG_FILE','seen_log.ser');
}

$lp->funcs['seenUpdate'] = function($message,$type) use (&$lp){
    $seen = $lp->plugVarGet('_LOG_PLUG','seen');
    $username = strtolower($message->username);
    if(!isset($seen[$username])){
      $seen[$username] = array();
    }
    $seen[$username][] = array(time(),$type,$message->data,$message->channel);
    file_put_contents(PLUGIN_DIR.LOG_FILE,serialize($seen));
    $lp->plugVarSet('_LOG_PLUG','seen',$seen);
};

$plugins = array(
  // Messages to handle and how to handle them
  array('message', 'onstart', 'func', function($message){
    if(!file_exists(PLUGIN_DIR.LOG_FILE)){
      file_put_contents(PLUGIN_DIR.LOG_FILE,serialize(array()));
    }
    $message->logpop->plugVarSet('_LOG_PLUG','seen',unserialize(file_get_contents(PLUGIN_DIR.LOG_FILE)));
    echo "Loaded log file...\n";
  }),
  array('message', 'quit', 'func', function($message){
    $message->logpop->seenUpdate($message,'QUIT');
    echo "[{$message->channel}] {$message->username} QUIT ({$message->data})\n";
  }),
  array('message', 'nick', 'func', function($message){
    if(empty($message->data)){
      $message->data = $message->middle[0];
    }
    $message->logpop->seenUpdate($message,'NICK');
    echo "[{$message->channel}] {$message->username} changed their nick to {$message->data}\n";
  }),
  array('message', 'part', 'func', function($message){
    $message->logpop->seenUpdate($message,'PART');
    echo "[{$message->channel}] {$message->username} PARTed\n";
  }),
  array('message', 'join', 'func', function($message){
    $message->logpop->seenUpdate($message,'JOIN');
    echo "[{$message->channel}] {$message->username} JOINed\n";
  }),
  array('message', 'privmsg', 'func', function($message){
    if(!$message->isCTCP){
      $message->logpop->seenUpdate($message,'PRIVMSG');
      echo "[{$message->channel}] {$message->username}: {$message->data}\n";
    } else if($message->CTCP == 'ACTION'){
      $message->logpop->seenUpdate($message,'ACTION');
      echo "[{$message->channel}] {$message->username} {$message->data}\n";
    }
  }),
  array('trigger', 'seen', 'func', function($args,$message){
    $from = strtolower($message->username);
    $find = $args[0];
    $channel = $message->channel;
    if($find == $message->logpop->getUsername()){
      $message->logpop->sendToChannel($channel,"Pretty sure that's me...");
      return;
    }
    if($find == $message->username){
      $message->logpop->sendToChannel($channel,"That's you.");
      return;
    }
    $seen = $message->logpop->plugVarGet('_LOG_PLUG','seen');
    if($seen == null || !isset($seen[$find])){
      $message->logpop->sendToChannel($channel,"$from: I've never heard of $find before...");
      return;
    }
    $last = $seen[$find];
    for($i=count($last);--$i>-1;){
      list($time,$type,$data,$channel) = $last[$i];
      if(empty($channel) || $channel == $message->channel){
        break;
      }
    }
    if($i < 0){
      $message->logpop->sendToChannel($message->channel,"$from: I haven't seen $find in {$message->channel}.");
      return;
    }
    $date = date('h:i:s A \o\n M d Y',$time);
    switch($type){
      case 'PRIVMSG':
        $str = "I last saw $find in $channel at $date saying '$data'";
        break;
      case 'ACTION':
        $str = "$channel at $date: $find $data";
        break;
      case 'JOIN':
        $str = "I last saw $find joining $channel at $date.";
        break;
      case 'PART':
        $str = "I last saw $find leaving $channel at $date. ($data)";
        break;
      case 'QUIT':
        $str = "I last saw $find quitting IRC at $date. ($data)";
        break;
      case 'NICK':
        $str = "I last saw $find changing their nick to $data at $date.";
        break;
    }
    $message->logpop->sendToChannel($channel,"{$message->username}: $str");
    if($type == 'NICK'){
      $m = new Message(":{$message->username}!{$message->realname}@{$message->host} PRIVMSG {$message->channel} :.seen $data\r\n",$message->logpop);
      $message->logpop->runMessage($m);
    }
  })
);

?>
