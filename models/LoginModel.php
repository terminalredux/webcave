<?php
namespace App\Models;
use Libs\Database\DbConnection;

class LoginModel
{
  /**
   * @return bool
   */
  public function login() : bool {
    $db = new DbConnection();
    $db = $db->connect();

    $sth = $db->prepare("SELECT id, password FROM user WHERE email = :email");
    $sth->execute([
      ':email' => $_POST['email']
    ]);

    $data = $sth->fetchAll();
    if (count($data) == 1) {
      if (password_verify($_POST['password'], $data[0]['password'])) {
        return true;
      }
    }
    return false;
  }

}
