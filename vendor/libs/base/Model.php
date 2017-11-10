<?php
namespace Libs\Base;

use Libs\Database\DbConnection;
use PDO;

/*
 * You have to provide in you Model that represents Database table
 * columns/fields like: id, status, created_at & updated_at
 *
 */
abstract class Model
{
  /**
   * Returns table name binded with model
   */
  abstract public static function tableName() : string;

  /**
   * Returns table relations with other tables
   */
  abstract public static function relations() : ? array;

  /**
   * Prepare model properties before save in database
   */
  abstract public function getForm() : void;

  /**
   * Saveing model in a database
   */
  public function save() : bool {
    $this->getForm();
    $model = (array) $this;
    unset($model['id']);

    $colNames = implode(', ', array_keys($model));
    $colValues = ':' . str_replace(', ', ', :', $colNames);
    $arrBind = array_combine(explode(', ', $colValues), array_values($model));

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("INSERT INTO " . static::tableName() . " (" . $colNames . ")
      VALUES (" . $colValues . ")");

      foreach ($arrBind as $key => &$value) {
        $statement->bindParam($key, $value);
      }

      $result = $statement->execute();
      $db = null;
    } catch (PDOException $e) {
      $result = false;
      $e->getMessage(); // Only in dev mode
    }
    return $result;
  }

  /**
   * Delete from the database
   */
  public function delete() : bool {
    $id = $this->id;
    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("DELETE FROM " . static::tableName() . " WHERE id = :id");
      $statement->bindParam(':id', $id);
      $result = $statement->execute();
      $db = null;
    } catch (PDOException $e) {
      $result = false;
      $e->getMessage(); // Only in dev mode
    }
    return $result;
  }

  /**
   * Update model in a database
   * @param bool $changeUpdatedAt if false column updated_at
   *                              in table will not be changed
   * @return int ID of updated model or 0 if failure
   */
  public function update(bool $changeUpdatedAt = true) : bool {
    if ($changeUpdatedAt) {
      $this->updated_at = time();
    }
    $model = (array) $this;
    $id = $this->id;

    $colNames = implode(', ', array_keys($model));
    $colValues = ':' . str_replace(', ', ', :', $colNames);
    $arrTemp = explode(',', $colNames);
    $arrBind = array_combine(explode(', ', $colValues), array_values($model));

    $prepareUpdateQuery = function($n) {
      $n = trim($n);
      return $n . ' = :' . $n;
    };

    $arrUpdate = array_map($prepareUpdateQuery, $arrTemp);
    $updateList = implode(', ', $arrUpdate);

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("UPDATE " . static::tableName() . " SET " . $updateList . "
      WHERE id = :id ");

      foreach ($arrBind as $key => &$value) {
        $statement->bindParam($key, $value);
      }

      $result = $statement->execute();
      $db = null;
    } catch (PDOException $e) {
      $result = false;
      $e->getMessage(); // Only in dev mode
    }
    return $result;
  }
}
