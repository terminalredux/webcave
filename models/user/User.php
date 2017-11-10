<?php
namespace App\Models\User;

use Libs\Base\Model;

class User extends Model
{
  public $id;
  public $username;
  public $alias;
  public $email;
  public $password;
  public $status;
  public $created_at;
  public $updated_at;

  const STATUS_UNACTIVE = 1;
  const STATUS_ACTIVE = 2;
  const STATUS_REMOVED = 3;
  
  /**
   * @inheritdoc
   */
  public static function tableName() : string {
    return "user";
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
    $this->username = $_POST['username'];
    $this->alias = $_POST['alias'];
    $this->email = $_POST['email'];
    $this->password = $this->setPassword();
    $this->status = self::STATUS_UNACTIVE;
    $this->created_at = time();
    $this->updated_at = time();
  }

  /**
   * TODO
   */
  private function setPassword() {
    return $_POST['password'];
  }

}
