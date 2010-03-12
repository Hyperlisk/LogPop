<?php

class Message {
  public $logpop;
  public $username = '';
  public $realname = '';
  public $host = '';
  public $channel = '';
  public $target = '';
  public $code = 'Error';
  public $middle = '';
  public $data = '';
  public $isCTCP = false;
  public $CTCP = '';
  public $args = array();
  public $params = array();
  private $raw = '';
  public function __construct($msg,&$logpop){
    $this->raw = $msg;
    //echo $msg;
    $this->logpop =& $logpop;

    if(!preg_match('/^:?((?:(?:[\w\d\-\/\.]+)|[^!\s]+(?:![^@\s]+(?:@[\w\d\-\.\/]+)?)?)? )?(\w+|\d\d\d)((?:(?: (?:[^: ][^ ]*))+)+)?(?: :([^\r\n]+))?/im',$msg,$matches)){
      return;
    }
    
    @list($m,$prefix,$code,$middle,$msg) = $matches;
    
    if(strpos($prefix,'@')){
      $p1 = explode('@',$prefix);
      $p2 = explode('!',$p1[0]);
      list($this->username,$this->realname,$this->host) = array(trim($p2[0]),isset($p2[1])?trim($p2[1]):'',isset($p1[1])?trim($p1[1]):'');
    } else {
      $prefix = trim($prefix);
      list($this->username,$this->realname,$this->host) = array($prefix,$prefix,$prefix);
    }
    $this->code = strtolower(trim($code));
    $this->middle = explode(' ',trim($middle));

    if(strlen($msg) > 2 && $msg[0] == chr(1) && $msg[strlen($msg)-1] == chr(1)){
      $this->isCTCP = true;
      $msg = explode(' ',substr($msg,1,strlen($msg)-2));
      $this->CTCP = array_shift($msg);
      $msg = implode(' ',$msg);
    }

    $this->data = trim($msg);
    $this->args = array_map('trim',explode(' ',$msg));
    $this->params = array_map('trim',explode(' ',$msg));

    $this->parseMiddle();
  }

  private function parseMiddle(){
    switch($this->code){
      case 'join':
        $this->channel = $this->data;
        break;
        
      case 'kick':
        list($this->channel,$this->target) = $this->middle;
        break;

      case 'privmsg':
      case 'part':
      default:
        $this->channel = $this->middle[count($this->middle)-1];
        break;
    }
  }

  public function remake(){
    return new Message($this->raw,$this->logpop);
  }
}

?>
