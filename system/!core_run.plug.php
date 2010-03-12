<?php

/*
\/  Run stuff...
/\
*/
$lp->funcs['run'] = function() use (&$lp){
  while(1){
    foreach($lp->servers as $k=>$server){
      $lp->current_server =& $server;
      if($server->run() === false){
        unset($lp->servers[$k]);
      }
    }
    usleep(1);
    if(count($lp->servers) == 0){
      break;
    }
  }
};
  
$lp->funcs['runTrigger'] = function($trigger,$message) use (&$lp){
  $test = $lp->plugVarGet('_CORE_IDENTITY',$message->username);
  if(empty($test)){
    $lp->verifyIdentity($trigger,$message);
  } else {
    $lp->runIt('trigger','system_triggers',$trigger,$message);
    $lp->runIt('trigger','general_triggers',$trigger,$message);
  }
};

$lp->funcs['runMessage'] = function($message) use (&$lp){
  $code = $message->code;
  $lp->runIt('message','system_messages',$code,$message);
  $lp->runIt('message','general_messages',$code,$message);
};

$lp->funcs['runIt'] = function($type,$what,$check,$message) use (&$lp){
  // First system plugins, then user plugins...
  $what = $lp->{$what};
  if(isset($what[$check])){
    foreach($what[$check] as $plugin){
      if($type == 'trigger' && $lp->getAccess($message->username) < (isset($plugin[2])?$plugin[2]:0)){
        $lp->sendToChannel($message->channel,"Sorry, ".$message->username.", you need more access to use $check... ".$lp->getAccess($message->username));
        continue;
      }
      $clone = $message->remake();
      switch($plugin[0]){
        case 'text':
          $find = array('$username','$channel','$host');
          $replace = array($message->username,$message->channel,$message->host);
          $args = $message->args;
          array_shift($args);
          for($i=0,$l=count($args);$i<$l;$i++){
            array_push($find,'$'.$i);
            array_push($replace,$args[$i]);
          }
          $lp->sendToChannel($message->channel,str_replace($find,$replace,$plugin[1]));
          break;
        case 'func':
          if($type == 'trigger'){
            $args = $message->args;
            array_shift($args);
            $plugin[1]($args,$message);
          } else {
            $plugin[1]($message);
          }
          break;
        default:
          echo "Unknown trigger type...";
          break;
      }
      $message = $clone;
    }
  }
};

?>