<?php
namespace App\Models\BaseCategory;

class BaseCategory extends \ActiveRecord\Model
{
  const HIDDEN = 1;
  const ACTIVE = 2;
  const REMOVED = 3;

  static $table_name = 'base_category';

  public static function getStatus() {
    return [
      1 => 'Ukryty',
      2 => 'Aktywny',
      3 => 'Usunięty'
    ];
  }

  public static function statusAlias() {
    return [
      1 => 'hidden',
      2 => 'active',
      3 => 'removed'
    ];
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
    $this->status = self::HIDDEN;
    $this->created_at = $time;
    $this->updated_at = $time;
  }

  public function loadEdition() {
    $this->name = $_POST['name'];
    $this->updated_at = time();
  }
}
