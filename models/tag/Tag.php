<?php
namespace App\Models\Tag;

class Tag extends \ActiveRecord\Model
{
  const ACTIVE = 1;

  static $table_name = 'tag';

  static $validates_presence_of  = [
    ['name', 'message' => ': musisz podać nazwę tagu', 'on' => 'create']
  ];
  static $validates_size_of = [
    ['name', 'within' => [1, 100], 'message' => ': nazwa tagu musi mieć od 1 do 100 znaków']
  ];

  public function loadCreate() {
    $this->name = $_POST['name'];
    $this->status = self::ACTIVE;
  }

}
