<?php
namespace App\Models\User;

class User extends \ActiveRecord\Model
{
  const ACTIVE = 1;
  const UNACTIVE = 2;
  const REMOVED = 3;

  static $table_name = 'user';

}
