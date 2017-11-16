<?php
namespace App\Models\BaseCategory;

class BaseCategory extends \ActiveRecord\Model
{
  const HIDDEN = 1;
  const ACTIVE = 2;
  const REMOVED = 3;

  static $table_name = 'base_category';

  static $has_many  = [
    ['categories' ,'class_name' => '\App\Models\Category\Category']
  ];

  static $validates_presence_of  = [
    ['name', 'message' => ': musisz podać nazwę kategorii bazowej', 'on' => 'create']
  ];
  static $validates_size_of = [
    ['name', 'within' => [2, 100], 'message' => ': nazwa musi mieć od 2 do 100 znaków']
  ];
  public function validate() {
    if ($this->validateUnique($this->name, $this->id)) {
      $this->errors->add("name", ": nazwa kategorii bazowej musi być unikalna a $this->name jest już zajęte");
    }
  }

  private function validateUnique($newBaseCategory, $newId) {
    $baseCetrgories = $this->all();
    $contains = false;
    foreach ($baseCetrgories as $baseCategory) {
      if (strtolower($baseCategory->name) == strtolower($newBaseCategory)) {
        if (isset($newId) && $newId == $baseCategory->id) {
          $contains = false;
        } else {
          $contains = true;
        }
      }
    }
    return $contains;
  }

  public static function getStatus() {
    return [
      1 => 'Ukryty',
      2 => 'Aktywny',
      3 => 'Usunięty'
    ];
  }

  public static function getStatusPrular() : array {
      return [
        'hidden' => 'ukryte',
        'active' => 'aktywne',
        'removed' => 'usunięte'
      ];
    }

  public static function statusAlias() {
    return [
      1 => 'hidden',
      2 => 'active',
      3 => 'removed'
    ];
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

  public function getCategoryRelations() : string {
    $categories = '';
    foreach($this->categories as $category) {
      $categories .= $category->name . '<br>';
    }
    return $categories;
  }

  public static function statusExists(string $status) : bool {
    if (in_array($status, self::statusAlias())) {
      return true;
    }
    return false;
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

  public function setStatus(string $status) : void {
    $stat = (int) array_flip($this->statusAlias())[$status];
    $this->status = $stat;
  }

  public function loadCreate() {
    $time = time();
    $this->name = $_POST['name'];
    $this->status = self::ACTIVE;
    $this->created_at = $time;
    $this->updated_at = $time;
  }

  public function loadEdition() {
    $this->name = $_POST['name'];
    $this->updated_at = time();
  }
}
