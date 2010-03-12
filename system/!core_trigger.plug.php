<?php

/*
\/  Trigger accessor method...
/\
*/
$lp->funcs['getTrigger'] = function() use (&$lp){
  return $lp->trigger;
};

/*
\/  Trigger setting method...
/\
*/
$lp->funcs['setTrigger'] = function($trigger) use (&$lp){
  $lp->trigger = $trigger;
};

?>