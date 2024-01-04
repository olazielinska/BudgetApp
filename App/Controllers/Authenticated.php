<?php

namespace App\Controllers;

use \App\Auth;

abstract class Authenticated extends \Core\Controller{

  protected function before(){
    $this->requireLogin();
  }

  protected function getUser(){
    return Auth::getUser();
  }
}