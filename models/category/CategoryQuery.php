<?php
namespace App\Models\Category;

use Libs\Base\Query;
use App\Models\Category\Category;

class CategoryQuery extends Query
{
  /**
   * @inheritdoc
   */
  public static function getTableName() : string {
    return Category::tableName();
  }

  /**
   * @inheritdoc
   */
  public static function mapping(array $row) {
    $cateogry = new Category();
    $cateogry->id = $row['id'];
    $cateogry->name = $row['name'];
    $cateogry->status = $row['status'];
    $cateogry->created_at = $row['created_at'];
    $cateogry->updated_at = $row['updated_at'];
    return $cateogry;
  }
}
