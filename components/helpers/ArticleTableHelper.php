<?php
namespace App\Components\Helpers;

use Libs\Base\TableFiller\TableDataHelper;
use App\Components\Helpers\ArticleHelper;

class ArticleTableHelper extends TableDataHelper
{
  public static function shortTitle(string $title) : string {
    $out = strlen($title) > 40 ? substr($title,0,40)."..." : $title;
    return $out;
  }

  public static function isPending(int $availableFrom) : bool {
    if ($availableFrom > time()) {
      return true;
    }
    return false;
  }

  public static function isPublicated(int $status) : bool {
    if ($status == ArticleHelper::PUBLICATED) {
      return true;
    }
    return false;
  }

  public static function isNotPublicated(int $status) : bool {
    if ($status == ArticleHelper::NOT_PUBLICATED) {
      return true;
    }
    return false;
  }

  public static function isSketch(int $status) : bool {
    if ($status == ArticleHelper::SKETCH) {
      return true;
    }
    return false;
  }

  public static function isRemoved(int $status) : bool {
    if ($status == ArticleHelper::REMOVED) {
      return true;
    }
    return false;
  }

  public static function statusClass(int $status) : string {
    if ($status == ArticleHelper::NOT_PUBLICATED) {
      $class = 'model-hidden';
    } elseif ($status == ArticleHelper::PUBLICATED) {
      $class = 'model-active';
    } elseif ($status == ArticleHelper::REMOVED) {
      $class = 'model-removed';
    } elseif ($status == ArticleHelper::SKETCH) {
      $class = 'model-sketch';
    }
    return $class;
  }


}
