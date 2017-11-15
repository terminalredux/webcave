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

  /**
   * Returns table name binded with model
   */
  abstract public static function tableName() : string;

  /**
   * Returns table relations with other tables
   * keys of the returned array is relation name eg:
   * 'user' => [
   *   'has' => 'one',
   *   'column' => 'user_id',
   *   'joined-table-column' => 'id',
   *   'model' => User::className()
   *   ],
   * 'comments' => [
   *   'has' => 'many',
   *   'column' => 'id',
   *   'joined-table-column' => 'article_id',
   *   'model' => Comment::className()
   *  ]
   */
  abstract public static function relations() : ? array;

  /**
   * Prepare model properties before save in database
   */
  abstract public function getForm() : void;

  /**
   * If you want to use method getBySlug you have to override
   * this method in concrete model and return slug property name
   */
  public static function slugProperty() : ? string {
    return null;
  }

  /**
   * Find model by slug column. If you want to use
   * this method you have to return slug property name
   * by overriden method slugPoprerty() in concrete model
   */
  public static function getBySlug(string $slug) : ? self {
    $classNamespace = '\\' . get_called_class();
    $slugPropertyName = $classNamespace::slugProperty();
    if (!$slugPropertyName) {
      return null;
    }
    $model = new $classNamespace;
    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . $classNamespace::tableName() . " WHERE $slugPropertyName = :slug");
      $statement->bindParam($slugPropertyName, $slug, PDO::PARAM_STR);
      $statement->execute();
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      $db = null;
      if ($row) {
        foreach($row as $key => $value) {
          $model->$key = $value;
        }
        return self::createRelationsProperties($model);
      }
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  private function initRelatedModels() : void {
    $relations = static::relations();

    for ($i = 1; $i <= count($relations); $i++) {
      $propertyName = key($relations);
      $modelNamespace = '\\' . current($relations)['model'];
      $this->$propertyName = new $modelNamespace;

      $fk_key = current($relations)['column'];
      var_dump($this);die;

      $this->$propertyName->getById();
      next($relations);
    }
  }

  /**
   * Method is used in getBy* methods (e.g. getById).
   * Create related models as a bace model properties
   * - based on relations settings in concrete model.
   * E.g. model News has a relation with
   * Category model: $news->category->id
   * returns ID of the category object
   */
  private static function createRelationsProperties(self $model) : self {
    $relations = static::relations();

    if ($relations) {
      $relNames = array_keys($relations);
      for ($i = 0; $i < count($relations); $i++) {
        $relConf = $relations[$relNames[$i]];
        $relPropName = $relNames[$i];
        $relModel = '\\' . $relConf['model'];
        $colName = $relConf['column'];
        $joinedCol = $relConf['joined-table-column'];
        if ($relConf['has'] == 'one') {
          $model->$relPropName = $relModel::getById($model->$colName, $joinedCol);
        } elseif ($relConf['has'] == 'many') {
          //TODO problemy z rekurencją...
          $model->$relPropName = $relModel::getAll($model->$colName, $joinedCol);
        }
      }
    }
    return $model;
  }

  /**
   * Finds all by column. Written for
   * relation has-many operation.
   * @param string $columnValue if you want to provide ID you have treat it like a string
   * @param string $columnName
   * @return array of Models
   */
  public static function getAll(string $columnValue, string $columnName) : ? array {
    $classNamespace = '\\' . get_called_class();

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . $classNamespace::tableName() . " WHERE $columnName = :value");
      $statement->bindParam(':value', $columnValue, PDO::PARAM_STR);
      $statement->execute();
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

      $db = null;
      $models = [];
      if ($rows) {
        foreach ($rows as $row) {
          $model = new $classNamespace;
          foreach ($row as $key => $value) {
            $model->$key = $value;
          }
          $model = self::createRelationsProperties($model);
          $models[] = $model;
        }
        //var_dump($models[1]->nick);die;
        //return self::createRelationsProperties($model);
        return $models;
      }
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  /**
   * Finds model in table by ID
   * @return Model||null
   */
  public static function getById(int $idValue, string $columnName = 'id') : ? self {
    $classNamespace = '\\' . get_called_class();
    $model = new $classNamespace;

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare("SELECT * FROM " . $classNamespace::tableName() . " WHERE $columnName = :id");
      $statement->bindParam(':id', $idValue, PDO::PARAM_INT);
      $statement->execute();
      $row = $statement->fetch(PDO::FETCH_ASSOC);

      $db = null;
      if ($row) {
        foreach($row as $key => $value) {
          $model->$key = $value;
        }
        return self::createRelationsProperties($model);
      }
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

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
