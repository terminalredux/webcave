<?php
namespace App\Components\Helpers;

class ArticleHelper
{
  /**
   * @const article statuses
   */
  const NOT_PUBLICATED = 1;
  const PUBLICATED = 2;
  const REMOVED = 3;
  const SKETCH = 4;

  const STATUS_PUBLIC = 'public';
  const STATUS_NOT_PUBLIC = 'notpublic';
  const STATUS_REMOVED = 'removed';
  const STATUS_SKETCH = 'sketch';


  public static function getStatus() {
    return [
      1 => 'Niepubliczny',
      2 => 'Publiczny',
      3 => 'Usunięty',
      4 => 'Szkic'
    ];
  }
}
