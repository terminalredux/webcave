<?php
namespace Libs\FlashMessage;

use Libs\Session\Session;

class Flash
{
  const FLASH_KEY = 'flash-message';

  /**
   * @param string $type success or error
   * @param string $message
   */
  public static function say(string $type, string $message) {
    Session::set(self::FLASH_KEY, [
      'type' => $type,
      'message' => $message,
      'toShow' => true
    ]);
  }

  /**
   * Init method
   */
  public static function showMessage() {
    $flash = Session::get(self::FLASH_KEY);
    if ($flash && $flash['toShow']) {
      $class = $flash['type'];
      $message = $flash['message'];
      include 'view.php';
    }
  }

  /**
   * Disable flash when has been alredy shown.
   */
  public static function disableFlash() {
    if (Session::get(Flash::FLASH_KEY)) {
      $_SESSION[Flash::FLASH_KEY]['toShow'] = false;
    }
  }

}
