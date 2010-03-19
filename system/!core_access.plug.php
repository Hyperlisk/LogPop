<?php

$lp->funcs['loadAccess'] = function() use (&$lp){
  if(!file_exists(CORE_DIR.'access.ser')){
    $lp->setAccess($lp->config['core'][1]['owner'],999);
  }
  $lp->access = unserialize(file_get_contents(CORE_DIR.'access.ser'));
};

$lp->funcs['setAccess'] = function($user,$access) use (&$lp){
  $lp->access[$user] = $access;
  file_put_contents(CORE_DIR.'access.ser',serialize($lp->access));
};

$lp->funcs['getAccess'] = function($user) use (&$lp){
  return isset($lp->access[$user])?$lp->access[$user]:0;
};

?>
