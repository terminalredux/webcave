<?php
namespace Libs\AccessControl;

use Libs\Session\Session;
use App\Controllers\ErrorController;

class AccessControl
{
  /**
   * Method allows only for logged users (admins)
   */
  public static function onlyForLogged() {
    if (!Session::get('logged')) {
      (new ErrorController)->pageNotFound("Page doesn't exists!");die;
    }
  }

  /**
   * Returns true if user is logged
   */
  public static function logged() : bool {
    if (Session::get('logged')) {
      return true;
    }
    return false;
  }
}
