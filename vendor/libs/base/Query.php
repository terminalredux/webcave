<?php
namespace Libs\Base;

use Libs\Database\DbConnection;
use Libs\Base\Model;
use PDO;

abstract class Query
{
  /**
   * Returns binded table name
   */
  abstract public static function getTableName() : string;

  /**
   * Map rows from the table with Model
   * @param array $row of DB table
   */
  abstract public static function mapping(array $row);

  /**
   * Returns Model by Id
   * @return Model or null
   */
  public static function getById(int $id) : ? Model {
    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . static::getTableName() . " WHERE id = :id");
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->execute();
      $row = $statement->fetch();
      $result = null;
      if ($row) {
        $result = static::mapping($row);
      }
      $db = null;
      return $result;
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  /**
   * Prepare Query only for getting all models
   * @param array $params from the controller
   * @return string SQL query
   */
  private static function prepareGetAllQuery(array $params = null) : string {
    $sql = "SELECT * FROM " . static::getTableName();
    if ($params) {
      if (isset($params['status'])) {
        $sql .= " WHERE status IN (";
        foreach ($params['status'] as $status) {
          $sql .= $status;
          if ($status != end($params['status'])) {
            $sql .= ',';
          }
        }
        $sql .= ")";
      }
      if (isset($params['sort'])) {
        $column = $params['sort'][0];
        $type = $params['sort'][1];
        $sql .= " ORDER BY " . $column . " " . $type;
      } else {
        $sql .= " ORDER BY updated_at DESC";
      }
    } else {
      $sql .= " ORDER BY updated_at DESC";
    }
    return $sql;
  }

  /**
   * Returns all Models
   * If user doesn't provides a params then
   * list is sorted desc by updated_at column
   * @param array $params
   * @return array of Models or null
   */
  public static function getAll(array $params = null) : ? array {
    $sql = self::prepareGetAllQuery($params);

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare($sql);
      $statement->execute();
      $rows = $statement->fetchAll();
      $models = [];
      if ($rows) {
        foreach($rows as $row) {
          $models[] = static::mapping($row);
        }
      }
      $db = null;
      return $models;
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }
}
