<?php
namespace Libs\FlashMessage;

/**
 * Provides configuration settings for
 * Flash Message in controller
 */
class FlashConf
{
  const SUC = 'success';
  const ERR = 'error';

  /**
   * Determintes if it's success or error falsh
   */
  private $type;

  /**
   * Content of flash messages
   */
  private $content;

  /**
   * @param string $type;
   * @param string $content;
   */
  public function set(string $type, string $content) : void {
    if ($type == self::SUC || $type == self::ERR) {
      $this->type = $type;
    }
    $this->content = $content;
  }

  public function isSuccess() {
    if ($this->type == self::SUC) {
      return true;
    }
    return false;
  }

  public function isError() {
    if ($this->type == self::ERR) {
      return true;
    }
    return false;
  }

  public function getContent() {
    return $this->content;
  }

}
