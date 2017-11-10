<?php
namespace Libs\Database;
require_once "config/db_connection.php";
use PDO;

class DbConnection
{
  public function connect() {
    $mysql_connect_str = "mysql:host=" . HOST . ";dbname=" . DBNAME;
    $dbConnection = new PDO($mysql_connect_str, USER, PASSWORD);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
  }
}
