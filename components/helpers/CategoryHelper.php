<?php
namespace App\Components\Helpers;

class CategoryHelper
{
  const STATUS_ACTIVE = 1;
  const STATUS_HIDDEN = 2;
  const STATUS_REMOVED = 3;

  public static function getStatus() : array {
    return [
      1 => 'Aktywny',
      2 => 'Ukryty',
      3 => 'Usunięty'
    ];
  }

  public static function bgColor() : array {
    return [
      1 => 'model-active',
      2 => 'model-hidden',
      3 => 'model-removed'
    ];
  }
}
