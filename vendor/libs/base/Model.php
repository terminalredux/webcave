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
   * Finds model in table by ID
   * @return Model||null
   */
  public static function getById(int $idValue, string $columnName = 'id') : ? self {
    $relationMap = [];
    //$relationMap = self::getsRelationsHasOne($relationMap);


    var_dump($sketchMap);die;

    //$sql = self::sqlJoining();
    //$sql .= $columnName . ' = ' . $idValue;
    //var_dump($sql);die;
  }

  /**
   * Returns sql select columns & joining
   * based on Model's relations settings.
   * @return string
   */
  private static function sqlJoining() : string {
    $relations = self::getsRelationsHasOne();

    $baseTable = static::tableName();
    $baseProperties = self::getProperties();
    foreach ($baseProperties as &$property) {
      $property = $baseTable . '.' . $property;
    }
    $colsFromBaseTabel = implode(', ', $baseProperties);
    $colsFromJoinedTables = '';
    foreach ($relations as $relation) {
      $modelNamespace = '\\' . $relation['model'];
      $modelTableName = $modelNamespace::tableName();
      $modelProperties = $modelNamespace::getProperties();
      foreach ($modelProperties as $key => &$value) {
        $value = $modelTableName . '.' . $value . ' AS ' . $modelTableName . '_' . $value;
      }
      $colsFromJoinedTables .= ', ' . implode(', ', $modelProperties);

    }
    $sql = 'SELECT ' . $colsFromBaseTabel . ' ' . $colsFromJoinedTables . ' FROM ' . $baseTable;

    foreach ($relations as $relation) {
      $modelNamespace = '\\' . $relation['model'];
      $modelTableName = $modelNamespace::tableName();
      $sql .= ' INNER JOIN ' . $modelTableName . ' ON ' . $baseTable . '.' . $relation['column'] . ' = ' . $modelTableName . '.' . $relation['joined-table-column'];
    }

    return $sql . ' WHERE ' . $baseTable . '.';
  }

  /**
   * Gets from Model's relations
   * only with status has = one
   * Adds to every relations parrent name if
   * exists.
   * @return array||null
   */
  private static function getsRelationsHasOne(array $relationMap) : ? array {
    $relations = static::relations();

    foreach ($relations as $key => $value) {
      if ($value['has'] == 'one') {
        $relationMap[$key] = $value;

        $classNamespace = '\\' . $value['model'];
        //$childRelations = $classNamespace::relations();



      }
    }
    var_dump($relationMap);die;
    return $relationMap;
  }

  //public static function getChildRelations() {
  //}

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
