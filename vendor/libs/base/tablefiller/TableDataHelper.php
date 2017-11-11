<?php
namespace Libs\Base\TableFiller;

class TableDataHelper
{
  public static function isUpdated(int $created_at, int $updated_at) : bool {
    if ($created_at < $updated_at) {
      return true;
    }
    return false;
  }
}
