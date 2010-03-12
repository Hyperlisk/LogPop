<?php

/*
\/ Username accessor method...
/\
*/
$lp->funcs['getDefaultUsername'] = function() use (&$lp){
  return $lp->default_username;
};

$lp->funcs['setDefaultUsername'] = function($name) use (&$lp){
  $lp->default_username = $name;
};

$lp->funcs['getUsername'] = function() use (&$lp){
  return $lp->current_server->getUsername();
};

?>