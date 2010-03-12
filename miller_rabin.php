<?php

/**
 * Miller-Rabin primality test in PHP
 * @author Nick Trevino <Nick@r0x.me>
 * @version 1.0
 * @copyright Copyright (c) 2009, http://php.r0x.me/
 */

function MillerRabin($n,$times=5){
  if(!is_string($n)){
    $n = (string)$n;
  }

  if(bccomp($n,"2") < 0 || bcmod($n,2) !== "1"){
    return 0;
  }

  // Initialize d and s
  $n_1 = bcsub($n,"1");
  $d = $n_1;
  $s = "0";

  // Factor out powers of 2, find s
  while(bcmod($d,2) === "0"){
    $d = bcdiv($d,"2");
    $s = bcadd($s,"1");
  }

  $correct = 0;
  $max = min((int)bcsub($n,"2"),2147483647);
  for($i=0;$i<$times;$i++){
    $tests++;
    $a = mt_rand(2,$max);
    $x = bcpowmod("$a",$d,$n);
    if(!bccomp($x,"1") || !bccomp($x,$n_1)){
      $correct++;
      continue;
    }
    for($r=1;$r<$s-1;$r++){
      $x = bcpowmod($x,"2",$n);
      if(!bccomp($x,$n_1)){
        $correct++;
        continue 2;
      }
      continue;
    }
  }

  return round($correct/$times*100);
}

function makeXDigitsPrime($digits,$chance=75){
  $digits = (int)$digits;
  $additions = array("2","6","4","2","4","2","4","6");
  $add_idx = -1;
  $num = chr(rand(0x31,0x39));
  for($i=1;$i<$digits;$i++){
    $num .= chr(rand(0x30,0x39));
  }
  $num = bcsub($num,bcadd(bcmod($num,"30"),"1"));
  do {
    $num = bcadd($num,$additions[(++$add_idx)%8]);
    if(bcpowmod("2",bcsub($num,"1"),$num) == 1 && MillerRabin($num,10) >= $chance){
      return $num;
    }
  } while(1);
}

function makeXBitsPrime($bits){
  $digits = strlen(bcpow("2",(string)$bits));
  return makeXDigitsPrime($digits);
}

function _base_convert ($numstring, $frombase, $tobase) {

   $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
   $tostring = substr($chars, 0, $tobase);

   $length = strlen($numstring);
   $result = '';
   for ($i = 0; $i < $length; $i++) {
       $number[$i] = strpos($chars, $numstring{$i});
   }
   do {
       $divide = 0;
       $newlen = 0;
       for ($i = 0; $i < $length; $i++) {
           $divide = $divide * $frombase + $number[$i];
           if ($divide >= $tobase) {
               $number[$newlen++] = (int)($divide / $tobase);
               $divide = $divide % $tobase;
           } elseif ($newlen > 0) {
               $number[$newlen++] = 0;
           }
       }
       $length = $newlen;
       $result = $tostring{$divide} . $result;
   }
   while ($newlen != 0);
   return $result;
}

// 28790371763450882572171724508125687710001699913945859260333191905705558785847118033607617140309485714993921789369127509258973327
// 6436094730018748473189097104726580764524621473557428163395545028009974067811435486983742029722651417

?>