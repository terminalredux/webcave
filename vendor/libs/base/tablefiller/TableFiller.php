<?php
namespace Libs\Base\TableFiller;

use Libs\Database\DbConnection;

abstract class TableFiller
{
  const WHERE_GROUP_DEFAULT = 'default';
  const SORT_GROUP_DEFAULT = 'default';
  /**
   * Returns table name
   */
  protected abstract function getTableName() : string;

  /**
   * Returns table relations with other tables
   */
  protected abstract function getRelations() : array;

  /**
   * Setter & Getter for the whereGroup property
   */
  public abstract function setWhereGroup(string $group) : bool;
  public abstract function getWhereGroup() : string;

  /**
   * Setter & Getter for the sortGroup property
   */
  public abstract function setSortGroup(string $group) : void;
  public abstract function getSortGroup() : string;

  /**
   * Gets SQL params like: columns
   *                       sort
   */
  protected abstract function getSql() : array;

  /**
   * Gets rows from table that depends
   * on provided SQL. First table in
   * array columns is the base table
   * You can only joins table with 'has one' relations! 
   */
  public function fetch() : ? array {
    $sql = $this->prepareSql();

    try {
      $db = new DbConnection();
      $db = $db->connect();
      $statement = $db->prepare($sql);
      $statement->execute();
      $rows = $statement->fetchAll();

      $data = [];
      if ($rows) {
        foreach($rows as $row) {
          $data[] = new class($row) {
            public function __construct(array $row) {
              foreach ($row as $key => $value) {
                $this->$key = $value;
              }
            }
          };
        }
      }
      $db = null;
      return $data;
    } catch (PDOException $e) {
      $e->getMessage();
    }
    return null;
  }

  /**
   * @return string SQL
   */
  private function prepareSql() : string {
    $sql  = $this->addJoins();
    $sql .= $this->addWhere();
    $sql .= $this->addOrder();
    return $sql;
  }

  /**
   * You can only joins table with 'has one' relations!
   * Init SQL: add columns and inner joins
   * For the columns from the outside the base table
   * adds aliases depends on thier table name prefix like: "user_id"
   * for the id column from the user table
   */
  private function addJoins() : string {
    $columns = [];
    $joinedTables = [];

    foreach ($this->getSql()['columns'] as $tableName => $tableColumns) {
      foreach ($tableColumns as $column) {
        if ($tableName == $this->getTableName()) {
          $columns[] = $tableName . '.' . $column;
        } else {
          $columns[] = $tableName . '.' . $column . ' AS ' . $tableName . '_' . $column;
        }
      }
      if ($tableName != $this->getTableName()) {
        $joinedTables[] = $tableName;
      }
    }

    $selectColumns = implode(', ', $columns);
    $sql = 'SELECT ' . $selectColumns . ' FROM ' . $this->getTableName();

    $relations = $this->getRelations();
    $relatedTables = [];
    foreach ($relations as $relation) {
      if ($relation['has'] == 'one') {
        $classNamespace = '\\' . $relation['model'];
        $relatedTables[] = $classNamespace::tableName();
      }
    }

    foreach ($joinedTables as $table) {
      if (in_array($table, $relatedTables)) {
        $sql .= ' INNER JOIN ' . $table . ' ON ' . $this->getTableName() . '.' . $relations[$table]['column'] . ' = ' . $table . '.' . $relations[$table]['joined-table-column'];
      }
    }
    return $sql;
  }

  /**
   * Choose where group from concrete TableFiller that depends
   * on user's settings. If user doesn't privide any group
   * method choose a default one in this case it is first element of
   * array. If where group doesnt exists, returns default group (first element)
   */
  private function chooseWhereGroup() : array {

    $whereGroup = $this->getWhereGroup();
    $groupExists = array_key_exists($whereGroup, $this->getSql()['where']);

    if ($whereGroup == self::WHERE_GROUP_DEFAULT || !$groupExists) {
      $whereArray = reset($this->getSql()['where']);
    } else {
      $whereArray = $this->getSql()['where'][$whereGroup];
    }
    return $whereArray;
  }

  /**
   * Sets WHERE
   */
  private function addWhere() : string {
    $sql = '';
    $firstWhere = true;
    $whereGroup = $this->chooseWhereGroup();

    foreach ($whereGroup as $column => $values) {
      if (!strpos($column, '.')) {
        $column = $this->getTableName() . '.' . $column;
      }
      $whereValues = [];
      foreach ($values as $value) {
        $whereValues[] = $value;
      }

      if ($firstWhere) {
        $sql .= ' WHERE ';
      } else {
        $sql .= ' AND ';
      }
      $sql .= $column . ' IN (' . implode(',', $whereValues) . ')';
      $firstWhere = false;
    }
    return $sql;
  }

  /**
   * Choose sort group from concrete TableFiller that depends
   * on user's settings. If user doesn't privide any group
   * method choose a default one in this case it is first element of
   * array. If select group doesnt exists, returns default group (first element)
   */
  private function chooseSortGroup() {
    $sortGroup = $this->getSortGroup();
    $groupExists = array_key_exists($sortGroup, $this->getSql()['sort']);

    if ($sortGroup == self::SORT_GROUP_DEFAULT || !$groupExists) {
      $sortArray = reset($this->getSql()['sort']);
    } else {
      $sortArray = $this->getSql()['sort'][$sortGroup];
    }
    return $sortArray;
  }

  /**
   * Sets ORDER BY
   */
  private function addOrder() : string {
    $columnsToOrder = [];
    $sortArray = $this->chooseSortGroup();

    foreach ($sortArray as $column => $value) {
      if (!strpos($column, '.')) {
        $column = $this->getTableName() . '.' . $column;
      }
      $columnsToOrder[] = $column . ' ' . $value;
    }
    $sql = ' ORDER BY ' . implode(', ', $columnsToOrder);
    return $sql;
  }

}
