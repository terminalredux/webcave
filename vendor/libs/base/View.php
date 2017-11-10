<?php
namespace Libs\Base;

use  Libs\Formatters\DateTimeFormatter;

class View
{
  /**
   * @param stirng $path e.g. 'article/index' in views folder
   * @param array $params available in your view
   */
  public function render(string $path, array $params = null) {
    $formatter = new DateTimeFormatter();
    if ($params) {
      foreach($params as $key => $value) {
        $$key = $value;
      }
    }
    require 'views/' . $path . '.php';
  }
}
