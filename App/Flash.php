<?php

namespace App;

class Flash{

  public static function addMessage($message){

    if(! isset($_SESSION['flash_notificiations'])){
      $_SESSION['flash_notificiations'] = [];
    }

    $_SESSION['flash_notificiations'][] = $message;
  }

  public static function getMessages(){
    if( isset($_SESSION['flash_notificiations'])){
      $messages = $_SESSION['flash_notificiations'];
      unset($_SESSION['flash_notificiations']);

      return $messages;
  }
} 
}