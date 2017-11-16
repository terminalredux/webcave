<?php
namespace App\Models\Category;

use App\Models\BaseCategory\BaseCategory;

class Category extends \ActiveRecord\Model
{
  const ACTIVE = 1;
  const HIDDEN = 2;
  const REMOVED = 3;

  static $table_name = 'category';

  static $belongs_to  = [
    ['base_category' ,'class_name' => '\App\Models\BaseCategory\BaseCategory', 'foreign_key' => 'base_category_id']
  ];

  static $validates_presence_of  = [
    ['name', 'message' => ': musisz podać nazwę kategorii', 'on' => 'create'],
    ['base_category_id', 'message' => ': musisz podać id kategorii bazowej', 'on' => 'create']
  ];
  static $validates_size_of = [
    ['name', 'within' => [2, 100], 'message' => ': nazwa musi mieć od 2 do 100 znaków']
  ];

  public function validate() {
    if ($this->validateBaseCategory($this->base_category_id) == false) {
      $this->errors->add("base_category_id", ": Zadano nieznane ID kategorii bazowej!");
    }
  }

  /**
   * You can only add category base with status
   * active or hidden. Removed not allowed
   */
  private function validateBaseCategory($baseCategoryId) {
    $baseCategories = BaseCategory::all([
      'conditions' => ['status' => [BaseCategory::ACTIVE, BaseCategory::HIDDEN]]
    ]);
    $allowedId = false;
    foreach ($baseCategories as $baseCategory) {
      if ($baseCategory->id == $baseCategoryId) {
        $allowedId = true;
      }
    }
    return $allowedId;
  }

  public function loadCreate() : void {
    $this->name = $_POST['name'];
    if (isset($_POST['base_category_id'])) {
      $this->base_category_id = $_POST['base_category_id'];
    }
    $this->status = self::ACTIVE;
  }

  public function loadEdition() {
    $this->name = $_POST['name'];
    $this->base_category_id = $_POST['base_category_id'];
  }

  public static function getStatus() {
    return [
      1 => 'Aktywny',
      2 => 'Ukryty',
      3 => 'Usunięty'
    ];
  }

  public static function statusAlias() {
    return [
      1 => 'active',
      2 => 'hidden',
      3 => 'removed'
    ];
  }

  public static function getStatusPrular() : array {
      return [
        'hidden' => 'ukryte',
        'active' => 'aktywne',
        'removed' => 'usunięte'
      ];
    }

  public static function statusExists(string $status) : bool {
    if (in_array($status, self::statusAlias())) {
      return true;
    }
    return false;
  }

  public static function getStatusByAlias($status) {
    return array_flip(self::statusAlias())[$status];
  }

  public function setStatusColor() : string {
    $class = '';
    if ($this->status == self::HIDDEN) {
      $class = 'model-hidden';
    } elseif ($this->status == self::ACTIVE) {
      $class = 'model-active';
    } elseif ($this->status == self::REMOVED) {
      $class = 'model-removed';
    }
    return $class;
  }

  public function setStatus(string $status) : void {
    $stat = (int) array_flip($this->statusAlias())[$status];
    $this->status = $stat;
  }

  public function isRemoved() : bool {
    if ($this->status == self::REMOVED) {
      return true;
    }
    return false;
  }

  public function isActive() : bool {
    if ($this->status == self::ACTIVE) {
      return true;
    }
    return false;
  }

  public function isHidden() : bool {
    if ($this->status == self::HIDDEN) {
      return true;
    }
    return false;
  }

  public function isEdited() : bool {
    if ($this->created_at != $this->updated_at) {
      return true;
    }
    return false;
  }

}
