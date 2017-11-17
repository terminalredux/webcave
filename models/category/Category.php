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
    if ($this->validateBaseCategory($this->status ,$this->base_category_id) == false) {
      $this->errors->add("base_category_id", ": Zadano nieznane ID kategorii bazowej!");
    }
  }

  /**
   * You can only add category base with status
   * active or hidden. Removed not allowed
   */
  private function validateBaseCategory($status, $baseCategoryId) {
    $baseCAtegoryStatus = [BaseCategory::ACTIVE, BaseCategory::HIDDEN, BaseCategory::REMOVED];
    $baseCategories = BaseCategory::all([
      'conditions' => ['status' => $baseCAtegoryStatus]
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
    $result = 1;
    if ($status == 'active') {
      $result = 1;
    } elseif ($status == 'hidden') {
      $result = 2;
    } elseif ($status == 'removed') {
      $result = 3;
    }
    return $result;
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

  /**
   * choose param for route afte change status,
   * creating new ones or edit
   */
  public function chooseParam() : string {
    $param = 'active';
    if ($this->status == Category::ACTIVE && $this->base_category->status == BaseCategory::ACTIVE) {
      $param = 'active';
    } elseif (($this->status == Category::HIDDEN && $this->base_category->status == BaseCategory::ACTIVE) ||
              ($this->status == Category::ACTIVE && $this->base_category->status == BaseCategory::HIDDEN) ||
              ($this->status == Category::HIDDEN && $this->base_category->status == BaseCategory::HIDDEN)) {
      $param = 'hidden';
    } elseif ($this->status == Category::REMOVED || $this->base_category->status == BaseCategory::REMOVED) {
      $param = 'removed';
    }
    return $param;
  }

  /**
   * Returns category list depedns on category
   * status and basecategory status
   * active:  c.status = active  && bc.status = active
   * hidden:  c.status = hidden  && bc.status = active | hidden
   * removed: c.status = removed && bc.status = active | hidden | removed
   */
  public static function availableCategorires(string $status, array $categories) : array {
    $categoryList = [];
    if ($status == 'active') {
      foreach ($categories as $category) {
        if ($category->status == Category::ACTIVE && $category->base_category_status == BaseCategory::ACTIVE) {
          $categoryList[] = $category;
        }
      }
    } elseif ($status == 'hidden') {
      foreach ($categories as $category) {
        if (($category->status == Category::ACTIVE && $category->base_category_status == BaseCategory::HIDDEN) ||
            ($category->status == Category::HIDDEN && $category->base_category_status == BaseCategory::ACTIVE) ||
            ($category->status == Category::HIDDEN && $category->base_category_status == BaseCategory::HIDDEN)) {
          $categoryList[] = $category;
        }
      }
    } elseif ($status == 'removed') {
      foreach ($categories as $category) {
        if (($category->status == Category::REMOVED || $category->base_category_status == BaseCategory::REMOVED)) {
          $categoryList[] = $category;
        }
      }
    }
    return $categoryList;
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
