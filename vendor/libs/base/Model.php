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
  public function __construct() {
  }

  private function initRelatedModels() : void {
    $relations = static::relations();

    for ($i = 1; $i <= count($relations); $i++) {
      $propertyName = key($relations);
      $modelNamespace = '\\' . current($relations)['model'];
      $this->$propertyName = new $modelNamespace;

      $fk_key = current($relations)['foreign-key'];
      var_dump($this);die;

      $this->$propertyName->getById();
      next($relations);
    }
  }

  public static function getBySlug(string $slug) : ? self {
    $classNamespace = '\\' . get_called_class();
    $model = new $classNamespace;

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . $classNamespace::tableName() . " WHERE slug = :slug");
      $statement->bindParam(':slug', $slug, PDO::PARAM_STR);
      $statement->execute();
      $row = $statement->fetch(PDO::FETCH_ASSOC);

      $db = null;
      if ($row) {
        foreach($row as $key => $value) {
          $model->$key = $value;
        }

        $relations = static::relations();
        if ($relations) {
          $relNames = array_keys($relations);
          for ($i = 0; $i < count($relations); $i++) {
            $relConf = $relations[$relNames[$i]];
            $relPropName = $relNames[$i];
            $relModel = '\\' . $relConf['model'];
            $fkColName = $relConf['foreign-key'];
            $model->$relPropName = $relModel::getById($model->$fkColName);
          }
        }
        return $model;
      }
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  /**
   * Finds model in table by ID
   */
  public static function getById(int $id) : ? self {
    $classNamespace = '\\' . get_called_class();
    $model = new $classNamespace;

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . $classNamespace::tableName() . " WHERE id = :id");
      $statement->bindParam(':id', $id, PDO::PARAM_INT);
      $statement->execute();
      $row = $statement->fetch(PDO::FETCH_ASSOC);

      $db = null;
      if ($row) {
        foreach($row as $key => $value) {
          $model->$key = $value;
        }

        $relations = static::relations();
        if ($relations) {
          $relNames = array_keys($relations);
          for ($i = 0; $i < count($relations); $i++) {
            $relConf = $relations[$relNames[$i]];
            $relPropName = $relNames[$i];
            $relModel = '\\' . $relConf['model'];
            $fkColName = $relConf['foreign-key'];
            $model->$relPropName = $relModel::getById($model->$fkColName);
          }
        }
        return $model;
      }
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }



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
   * Gets class properties that represents all of the
   * related table's columns. Remove properties for
   * the models in relations. Returns only properties
   * that represent base table columns.
   */
  public static function getProperties() : array {
    $class = '\\' . get_called_class();
    $relations = static::relations();
    if ($relations) {
      $relationProperties = array_keys(static::relations());
      $allProperties = array_keys(get_object_vars(new $class));
      $baseTableProperties = array_diff($allProperties, $relationProperties);
    } else {
      $baseTableProperties = array_keys(get_object_vars(new $class));
    }
    return $baseTableProperties;
  }

  /**
   * Gets full name of the concrette model with namespace
   */
  public static function className() : string {
    return get_called_class();
  }

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
