<?php
namespace App\Models\Category;

use Libs\Base\Model;
use App\Models\{
  BaseCategory\BaseCategory
};

class Category extends Model
{
  public $id;
  public $base_category_id;
  public $name;
  public $status;
  public $created_at;
  public $updated_at;

  const STATUS_ACTIVE = 1;
  const STATUS_HIDDEN = 2;
  const STATUS_REMOVED = 3;

  /**
   * @inheritdoc
   */
  public static function tableName() : string {
    return "category";
  }

  /**
   * @inheritdoc
   */
  public static function relations() : ? array {
    return null;
  }

  /**
   * @inheritdoc
   */
  public function getForm() : void {
    $this->name = $_POST['name'];
    $this->base_category_id = 1;
    $this->status = self::STATUS_ACTIVE;
    $this->created_at = time();
    $this->updated_at = time();
  }

  /**
   * Changes status of category to REMOVED
   */
  public function softRemove() : void {
    $this->status = Category::STATUS_REMOVED;
  }

  /**
   * Change category status to ACTIVE
   */
  public function activation() : void {
    $this->status = Category::STATUS_ACTIVE;
  }

  /**
   * Change category status to HIDDEN
   */
  public function hide() : void {
    $this->status = Category::STATUS_HIDDEN;
  }

  /**
   * Change category name
   */
  public function editName() : void {
    $this->name = $_POST['name'];
  }

}
