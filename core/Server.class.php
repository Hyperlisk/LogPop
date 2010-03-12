<?php

/*
\/  log[pop] Server Class
/\  A class to represent a server object.
\/
/\  Author: Nick Trevino
*/

class Server {

  // Server data...
  private $host = false;
  private $port = false;
  private $pass = false;

  // Socket for the connection
  private $socket = false;

  // Username being used on this server
  private $username = '';

  // Link back to log[pop]
  private $logpop = false;

  /*
  \/  Get the bot username...
  /\  
  */
  public function __construct(&$logpop){
    $this->logpop =& $logpop;
    //debug_print_backtrace();exit;
    $this->username = $this->logpop->getDefaultUsername();
  }

  /*
  \/  Username accessor method...
  /\  
  */
  public function getUsername(){
    return $this->username;
  }

  /*
  \/  Overloads function calls.
  /\  
  */
  public function __call($func,$args){
    $argc = count($args);
    switch($func){
      case 'connect':
        if($argc < 2){
          $func = 'connect_1';
        } else {
          $func = 'connect_2';
        }
        break;

      default:
        break;
    }
    return call_user_func_array(array($this,$func),$args);
  }

  /*
  \/  Connects with a string in the form of
  /\  irc://password@www.host.tld:port/channel
  */
  public function connect_1($str){
    $url = parse_url($str);

    // Make sure stuff is there...
    $url['host'] = isset($url['host'])?$url['host']:null;
    $url['port'] = isset($url['port'])?$url['port']:6667;
    $url['user'] = isset($url['user'])?substr($url['user'],1):'';
    $channel = explode('/',$str);
    $channel = $channel[count($channel)-1];

    // Call other connect function to actually connect...
    return $this->connect_2($url['host'],$url['port'],$url['user'],$channel);
  }

  /*
  \/  Connects to the specified server
  /\
  */
  public function connect_2($host=null,$port=6667,$pass='',$channel=''){
    if(empty($host)){
      return false;
    }
    $this->socket = fsockopen($host,(int)$port);
    if($this->socket === false){
      return false;
    }
    stream_set_timeout($this->socket,1);
    stream_set_blocking($this->socket,0);
    $this->setNick($this->username);
    $this->send('USER '.$this->username.' '.$this->username.' '.$this->username.' :'.$this->username);
    if(!empty($channel)){
      $this->join($channel);
    }
    return true;
  }

  /*
  \/  Get more data~!
  /\
  */
  public function run(){
    if($this->socket === false){
      return false;
    }
    $msg = fgets($this->socket,512);
    $msg = (!empty($msg)?(new Message($msg,$this->logpop)):false);
    if($msg !== false){
      $this->logpop->runMessage($msg);
    }
    return true;
  }

  /*
  \/  Send data to the server
  /\
  */
  public function send($data){
    if(empty($data)){
      return false;
    }
    fputs($this->socket,$data."\r\n");
    return true;
  }

  /*
  \/  Send a PRIVMSG
  /\
  */
  public function sendMessage($where,$data){
    if(empty($where)){
      return false;
    }
    return $this->send('PRIVMSG '.$where.' :'.$data);
  }

  /*
  \/  Send a NOTICE
  /\
  */
  public function sendNotice($where,$data){
    if(empty($where)){
      return false;
    }
    return $this->send('NOTICE '.$where.' :'.$data);
  }

  /*
  \/  Set the username being used on the server
  /\
  */
  public function setNick($nick){
    if(empty($nick)){
      return false;
    }
    $this->send('NICK '.$nick);
    $this->username = $nick;
    return $this->run();
  }

  public function join($channel){
    return $this->send('JOIN '.$channel);
  }

  public function part($channel){
    return $this->send('PART '.$channel);
  }

  public function quit($message=''){
    $ret = $this->send('QUIT :'.$message);
    fclose($this->socket);
    $this->socket = false;
    return $ret;
  }

}

?>